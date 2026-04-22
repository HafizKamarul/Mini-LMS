<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Result;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentExamController extends Controller
{
    public function index(Request $request): View
    {
        $now     = now();
        $student = $request->user();

        $activeExams = Exam::query()
            ->where('is_published', true)
            ->where(function ($query) use ($now) {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->withCount('questions')
            ->orderBy('starts_at')
            ->get();

        $submissions = Submission::query()
            ->where('student_id', $student->id)
            ->whereIn('exam_id', $activeExams->pluck('id'))
            ->get()
            ->keyBy('exam_id');

        return view('student.exams.index', compact('activeExams', 'submissions'));
    }

    public function start(Request $request, Exam $exam): RedirectResponse
    {
        if (! $this->isExamActive($exam)) {
            return redirect()
                ->route('student.exams.index')
                ->with('warning', 'This exam is not active right now.');
        }

        $submission = Submission::query()->firstOrCreate(
            [
                'exam_id'    => $exam->id,
                'student_id' => $request->user()->id,
            ],
            [
                'started_at' => now(),
                'status'     => 'in_progress',
            ]
        );

        if ($submission->status !== 'in_progress') {
            return redirect()
                ->route('student.exams.index')
                ->with('warning', 'This exam has already been submitted.');
        }

        if (! $submission->started_at) {
            $submission->update(['started_at' => now()]);
            $submission->refresh();
        }

        return redirect()->route('student.exams.attempt', $exam);
    }

    public function attempt(Request $request, Exam $exam): RedirectResponse|View
    {
        $submission = Submission::query()->firstOrCreate(
            [
                'exam_id'    => $exam->id,
                'student_id' => $request->user()->id,
            ],
            [
                'started_at' => now(),
                'status'     => 'in_progress',
            ]
        );

        if ($submission->status !== 'in_progress') {
            return redirect()
                ->route('student.exams.index')
                ->with('warning', 'This exam session is already submitted.');
        }

        if (! $submission->started_at) {
            $submission->update(['started_at' => now()]);
            $submission->refresh();
        }

        $deadline = $this->resolveDeadline($exam, $submission);

        if (now()->greaterThanOrEqualTo($deadline)) {
            $this->finalizeSubmission($submission);

            return redirect()
                ->route('student.exams.index')
                ->with('warning', 'Time is up. Your exam has been auto-submitted.');
        }

        $questions = $exam->questions()
            ->with(['options' => fn ($query) => $query->orderBy('order')])
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $answersByQuestion = $submission->answers
            ->keyBy('question_id');

        $questionPayload = $questions->map(function (Question $question) use ($answersByQuestion) {
            /** @var Answer|null $answer */
            $answer = $answersByQuestion->get($question->id);

            return [
                'id'            => $question->id,
                'question_text' => $question->question_text,
                'type'          => $question->type,
                'marks'         => $question->marks,
                'options'       => $question->options->map(fn ($option) => [
                    'id'          => $option->id,
                    'option_text' => $option->option_text,
                ])->values()->all(),
                'answer'        => [
                    'question_option_id' => $answer?->question_option_id,
                    'answer_text'        => $answer?->answer_text,
                    'is_flagged'         => (bool) ($answer?->is_flagged ?? false),
                ],
            ];
        })->values();

        return view('student.exams.attempt', [
            'exam'       => $exam,
            'submission' => $submission,
            'deadline'   => $deadline,
            'questions'  => $questionPayload,
        ]);
    }

    public function resultDetail(Request $request, Result $result): View|RedirectResponse
    {
        // Students can only see their own results, and only if published
        if ($result->student_id !== $request->user()->id) {
            abort(403);
        }

        if (! $result->published_at) {
            return redirect()
                ->route('student.dashboard')
                ->with('warning', 'This result has not been published yet.');
        }

        $result->load([
            'exam:id,title,total_marks',
            'submission.answers.question.options',
        ]);

        $answers = $result->submission->answers
            ->sortBy(fn ($a) => $a->question->order ?? $a->question->id)
            ->values();

        return view('student.results.show', compact('result', 'answers'));
    }

    public function saveAnswer(Request $request, Exam $exam, Question $question): JsonResponse
    {
        if ($question->exam_id !== $exam->id) {
            abort(404);
        }

        $submission = Submission::query()->where([
            'exam_id'    => $exam->id,
            'student_id' => $request->user()->id,
            'status'     => 'in_progress',
        ])->first();

        if (! $submission) {
            return response()->json(['message' => 'No active submission found.'], 422);
        }

        if (now()->greaterThanOrEqualTo($this->resolveDeadline($exam, $submission))) {
            $this->finalizeSubmission($submission);

            return response()->json(['message' => 'Time is up. Exam auto-submitted.'], 422);
        }

        $validated = $request->validate([
            'question_option_id' => [
                'nullable',
                'integer',
                Rule::exists('question_options', 'id')->where('question_id', $question->id),
            ],
            'answer_text' => ['nullable', 'string'],
            'is_flagged'  => ['nullable', 'boolean'],
        ]);

        $isSubjective = $question->type === 'short_answer';

        $answerData = [
            'question_option_id' => $isSubjective ? null : ($validated['question_option_id'] ?? null),
            'answer_text'        => $isSubjective ? trim((string) ($validated['answer_text'] ?? '')) : null,
            'is_flagged'         => (bool) ($validated['is_flagged'] ?? false),
        ];

        $answerData['answer_text'] = $answerData['answer_text'] === '' ? null : $answerData['answer_text'];

        Answer::query()->updateOrCreate(
            [
                'submission_id' => $submission->id,
                'question_id'   => $question->id,
            ],
            $answerData
        );

        return response()->json([
            'message'  => 'Answer saved.',
            'saved_at' => now()->toIso8601String(),
        ]);
    }

    public function submit(Request $request, Exam $exam): RedirectResponse
    {
        $submission = Submission::query()->where([
            'exam_id'    => $exam->id,
            'student_id' => $request->user()->id,
        ])->first();

        if (! $submission) {
            return redirect()
                ->route('student.exams.index')
                ->with('warning', 'No active submission found for this exam.');
        }

        if (! $this->finalizeSubmission($submission)) {
            return redirect()
                ->route('student.exams.index')
                ->with('warning', 'This exam was already submitted.');
        }

        return redirect()
            ->route('student.exams.index')
            ->with('success', 'Exam submitted successfully.');
    }

    private function isExamActive(Exam $exam): bool
    {
        $now = now();

        if (! $exam->is_published) {
            return false;
        }

        if ($exam->starts_at && $exam->starts_at->greaterThan($now)) {
            return false;
        }

        if ($exam->ends_at && $exam->ends_at->lessThan($now)) {
            return false;
        }

        return true;
    }

    private function resolveDeadline(Exam $exam, Submission $submission): Carbon
    {
        $sessionDeadline = $submission->started_at
            ? $submission->started_at->copy()->addMinutes($exam->duration_minutes)
            : now()->addMinutes($exam->duration_minutes);

        if (! $exam->ends_at) {
            return $sessionDeadline;
        }

        return $exam->ends_at->lessThan($sessionDeadline) ? $exam->ends_at->copy() : $sessionDeadline;
    }

    private function finalizeSubmission(Submission $submission): bool
    {
        $updated = Submission::query()
            ->where('id', $submission->id)
            ->where('status', 'in_progress')
            ->update([
                'status'       => 'submitted',
                'submitted_at' => now(),
                'updated_at'   => now(),
            ]);

        if ($updated === 0) {
            return false;
        }

        $submission->refresh();
        $this->autoMarkSubmission($submission);

        return true;
    }

    private function autoMarkSubmission(Submission $submission): void
    {
        $submission->loadMissing([
            'exam.questions.options',
            'answers',
        ]);

        $questions         = $submission->exam->questions;
        $answersByQuestion = $submission->answers->keyBy('question_id');

        $totalMarks = (float) $questions->sum(fn (Question $question) => (float) $question->marks);
        $score      = 0.0;

        foreach ($questions as $question) {
            /** @var Answer|null $answer */
            $answer = $answersByQuestion->get($question->id);

            if (! $answer) {
                continue;
            }

            $awardedMarks = 0.0;
            $isCorrect    = false;

            if ($question->type === 'short_answer') {
                $keywords = collect($question->keywords ?? [])
                    ->map(fn ($keyword) => strtolower(trim((string) $keyword)))
                    ->filter()
                    ->unique()
                    ->values();

                $answerText = strtolower(trim((string) ($answer->answer_text ?? '')));

                if ($keywords->isNotEmpty() && $answerText !== '') {
                    $matchedKeywords = $keywords->filter(
                        fn (string $keyword) => str_contains($answerText, $keyword)
                    )->count();

                    $matchRatio = $matchedKeywords / $keywords->count();

                    if ($matchRatio >= 0.5) {
                        $awardedMarks = (float) $question->marks;
                        $isCorrect    = true;
                    }
                }
            } else {
                $correctOption = $question->options->firstWhere('is_correct', true);

                if ($correctOption && (int) $answer->question_option_id === (int) $correctOption->id) {
                    $awardedMarks = (float) $question->marks;
                    $isCorrect    = true;
                }
            }

            $answer->update([
                'is_correct'    => $isCorrect,
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

        Result::query()->updateOrCreate(
            ['submission_id' => $submission->id],
            [
                'exam_id'     => $submission->exam_id,
                'student_id'  => $submission->student_id,
                'score'       => round($score, 2),
                'total_marks' => round($totalMarks, 2),
                'percentage'  => $percentage,
                'time_taken'  => $timeTaken,
                'passed'      => $percentage >= 50,
            ]
        );
    }
}