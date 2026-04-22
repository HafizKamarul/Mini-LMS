<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Result;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ExamApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $now = now();
        $studentId = $request->user()->id;

        $exams = Exam::query()
            ->where('is_published', true)
            ->where(function (Builder $query) use ($now): void {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function (Builder $query) use ($now): void {
                $query->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->withCount('questions')
            ->with([
                'submissions' => function (Builder $query) use ($studentId): void {
                    $query->where('student_id', $studentId)
                        ->select('id', 'exam_id', 'status', 'started_at', 'submitted_at');
                },
            ])
            ->orderBy('starts_at')
            ->orderBy('id')
            ->get();

        $payload = $exams->map(function (Exam $exam): array {
            $submission = $exam->submissions->first();

            return [
                'id' => $exam->id,
                'title' => $exam->title,
                'description' => $exam->description,
                'duration_minutes' => $exam->duration_minutes,
                'total_marks' => (float) $exam->total_marks,
                'starts_at' => $exam->starts_at?->toIso8601String(),
                'ends_at' => $exam->ends_at?->toIso8601String(),
                'questions_count' => $exam->questions_count,
                'submission' => $submission ? [
                    'id' => $submission->id,
                    'status' => $submission->status,
                    'started_at' => $submission->started_at?->toIso8601String(),
                    'submitted_at' => $submission->submitted_at?->toIso8601String(),
                ] : null,
            ];
        })->values();

        return response()->json([
            'message' => 'Exams fetched successfully.',
            'data' => $payload,
        ]);
    }

    public function storeResult(Request $request, int $exam_id): JsonResponse
    {
        $student = $request->user();

        $exam = Exam::query()
            ->where('id', $exam_id)
            ->where('is_published', true)
            ->first();

        if (! $exam) {
            return response()->json([
                'message' => 'Exam not found.',
            ], 404);
        }

        $validated = $request->validate([
            'answers' => ['nullable', 'array'],
            'answers.*.question_id' => [
                'required',
                'integer',
                Rule::exists('questions', 'id')->where('exam_id', $exam->id),
            ],
            'answers.*.question_option_id' => ['nullable', 'integer', 'exists:question_options,id'],
            'answers.*.answer_text' => ['nullable', 'string'],
            'answers.*.is_flagged' => ['nullable', 'boolean'],
        ]);

        $existingSubmission = Submission::query()->where([
            'exam_id' => $exam->id,
            'student_id' => $student->id,
        ])->first();

        if ($existingSubmission && $existingSubmission->status !== 'in_progress') {
            return response()->json([
                'message' => 'This exam has already been submitted. Double submission is not allowed.',
            ], 409);
        }

        $result = DB::transaction(function () use ($exam, $student, $validated): Result {
            $submission = Submission::query()->firstOrCreate(
                [
                    'exam_id' => $exam->id,
                    'student_id' => $student->id,
                ],
                [
                    'started_at' => now(),
                    'status' => 'in_progress',
                ]
            );

            if ($submission->status !== 'in_progress') {
                abort(409, 'This exam has already been submitted. Double submission is not allowed.');
            }

            if (! $submission->started_at) {
                $submission->update(['started_at' => now()]);
                $submission->refresh();
            }

            $this->persistProvidedAnswers($submission, $validated['answers'] ?? []);

            if (! $this->hasAnsweredAtLeastOneQuestion($submission)) {
                abort(422, 'Cannot submit an empty exam. Please answer at least one question.');
            }

            $submission->update([
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            return $this->autoMarkSubmission($submission->fresh());
        });

        $result->loadMissing('exam:id,title');

        return response()->json([
            'message' => 'Result generated successfully.',
            'data' => [
                'id' => $result->id,
                'exam' => [
                    'id' => $result->exam_id,
                    'title' => $result->exam->title,
                ],
                'student_id' => $result->student_id,
                'score' => (float) $result->score,
                'total_marks' => (float) $result->total_marks,
                'percentage' => (float) $result->percentage,
                'passed' => (bool) $result->passed,
                'time_taken' => (int) $result->time_taken,
                'published_at' => $result->published_at?->toIso8601String(),
                'created_at' => $result->created_at?->toIso8601String(),
            ],
        ]);
    }

    public function transcript(Request $request, int $id): JsonResponse
    {
        $authUser = $request->user();

        if ((int) $authUser->id !== (int) $id && $authUser->role !== 'admin') {
            return response()->json([
                'message' => 'You are not authorized to view this transcript.',
            ], 403);
        }

        $student = User::query()
            ->where('id', $id)
            ->where('role', 'student')
            ->first();

        if (! $student) {
            return response()->json([
                'message' => 'Student not found.',
            ], 404);
        }

        $results = Result::query()
            ->where('student_id', $student->id)
            ->with('exam:id,title,total_marks')
            ->orderByDesc('created_at')
            ->get();

        $attemptedExams = $results->count();
        $averagePercentage = $attemptedExams > 0 ? round((float) $results->avg('percentage'), 2) : 0.0;

        return response()->json([
            'message' => 'Transcript fetched successfully.',
            'data' => [
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                ],
                'summary' => [
                    'attempted_exams' => $attemptedExams,
                    'passed_exams' => $results->where('passed', true)->count(),
                    'failed_exams' => $results->where('passed', false)->count(),
                    'average_percentage' => $averagePercentage,
                ],
                'results' => $results->map(function (Result $result): array {
                    return [
                        'id' => $result->id,
                        'exam' => [
                            'id' => $result->exam_id,
                            'title' => $result->exam?->title,
                            'total_marks' => $result->exam ? (float) $result->exam->total_marks : (float) $result->total_marks,
                        ],
                        'score' => (float) $result->score,
                        'total_marks' => (float) $result->total_marks,
                        'percentage' => (float) $result->percentage,
                        'passed' => (bool) $result->passed,
                        'time_taken' => (int) $result->time_taken,
                        'published_at' => $result->published_at?->toIso8601String(),
                        'created_at' => $result->created_at?->toIso8601String(),
                    ];
                })->values(),
            ],
        ]);
    }

    /**
     * @param array<int, array<string, mixed>> $answers
     */
    private function persistProvidedAnswers(Submission $submission, array $answers): void
    {
        if ($answers === []) {
            return;
        }

        $questionIds = collect($answers)
            ->pluck('question_id')
            ->unique()
            ->values();

        $questions = Question::query()
            ->where('exam_id', $submission->exam_id)
            ->whereIn('id', $questionIds)
            ->with('options:id,question_id')
            ->get()
            ->keyBy('id');

        foreach ($answers as $answerInput) {
            $question = $questions->get((int) $answerInput['question_id']);

            if (! $question) {
                continue;
            }

            $optionId = $answerInput['question_option_id'] ?? null;
            if ($optionId !== null && ! $question->options->contains('id', (int) $optionId)) {
                $optionId = null;
            }

            $isSubjective = $question->type === 'short_answer';

            $answerData = [
                'question_option_id' => $isSubjective ? null : $optionId,
                'answer_text' => $isSubjective ? trim((string) ($answerInput['answer_text'] ?? '')) : null,
                'is_flagged' => (bool) ($answerInput['is_flagged'] ?? false),
            ];

            $answerData['answer_text'] = $answerData['answer_text'] === '' ? null : $answerData['answer_text'];

            Answer::query()->updateOrCreate(
                [
                    'submission_id' => $submission->id,
                    'question_id' => $question->id,
                ],
                $answerData
            );
        }
    }

    private function autoMarkSubmission(Submission $submission): Result
    {
        $submission->loadMissing([
            'exam.questions.options',
            'answers',
        ]);

        $questions = $submission->exam->questions;
        $answersByQuestion = $submission->answers->keyBy('question_id');

        $totalMarks = (float) $questions->sum(fn (Question $question): float => (float) $question->marks);
        $score = 0.0;

        foreach ($questions as $question) {
            $answer = $answersByQuestion->get($question->id);

            if (! $answer) {
                continue;
            }

            $awardedMarks = 0.0;
            $isCorrect = false;

            if ($question->type === 'short_answer') {
                $keywords = collect($question->keywords ?? [])
                    ->map(fn ($keyword) => strtolower(trim((string) $keyword)))
                    ->filter()
                    ->unique()
                    ->values();

                $answerText = strtolower(trim((string) ($answer->answer_text ?? '')));

                if ($keywords->isNotEmpty() && $answerText !== '') {
                    $matchedKeywords = $keywords->filter(
                        fn (string $keyword): bool => str_contains($answerText, $keyword)
                    )->count();

                    $matchRatio = $matchedKeywords / $keywords->count();

                    if ($matchRatio >= 0.5) {
                        $awardedMarks = (float) $question->marks;
                        $isCorrect = true;
                    }
                }
            } else {
                $correctOption = $question->options->firstWhere('is_correct', true);

                if ($correctOption && (int) $answer->question_option_id === (int) $correctOption->id) {
                    $awardedMarks = (float) $question->marks;
                    $isCorrect = true;
                }
            }

            $answer->update([
                'is_correct' => $isCorrect,
                'marks_awarded' => $awardedMarks,
            ]);

            $score += $awardedMarks;
        }

        $percentage = $totalMarks > 0
            ? round(($score / $totalMarks) * 100, 2)
            : 0.0;

        $timeTaken = 0;
        if ($submission->started_at && $submission->submitted_at) {
            $timeTaken = max(0, $submission->started_at->diffInSeconds($submission->submitted_at, false));
        }

        $submission->update([
            'score' => round($score, 2),
        ]);

        return Result::query()->updateOrCreate(
            ['submission_id' => $submission->id],
            [
                'exam_id' => $submission->exam_id,
                'student_id' => $submission->student_id,
                'score' => round($score, 2),
                'total_marks' => round($totalMarks, 2),
                'percentage' => $percentage,
                'time_taken' => $timeTaken,
                'passed' => $percentage >= 50,
            ]
        );
    }

    private function hasAnsweredAtLeastOneQuestion(Submission $submission): bool
    {
        return $submission->answers()
            ->where(function (Builder $query): void {
                $query->whereNotNull('question_option_id')
                    ->orWhere(function (Builder $textQuery): void {
                        $textQuery->whereNotNull('answer_text')
                                ->whereRaw('TRIM(answer_text) <> \'\'');
                    });
            })
            ->exists();
    }
}