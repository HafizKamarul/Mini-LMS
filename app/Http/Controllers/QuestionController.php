<?php

namespace App\Http\Controllers;

use App\Exports\ExamQuestionsExport;
use App\Imports\QuestionsImport;
use App\Models\Exam;
use App\Models\QuestionBankQuestion;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Services\QuestionBankService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class QuestionController extends Controller
{
    public function __construct(private readonly QuestionBankService $questionBankService)
    {
    }

    public function index(Exam $exam): View
    {
        $questions = $exam->questions()
            ->with('options')
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $bankQuestions = QuestionBankQuestion::query()
            ->with('options')
            ->latest()
            ->limit(25)
            ->get();

        return view('admin.questions.index', compact('exam', 'questions', 'bankQuestions'));
    }

    public function create(Exam $exam): View
    {
        return view('admin.questions.create', compact('exam'));
    }

    public function edit(Exam $exam, Question $question): View
    {
        abort_if($question->exam_id !== $exam->id, 404);

        $question->load('options');

        return view('admin.questions.edit', compact('exam', 'question'));
    }

    public function update(Request $request, Exam $exam, Question $question): RedirectResponse
    {
        abort_if($question->exam_id !== $exam->id, 404);

        $type = $question->type === 'single_choice' ? 'mcq' : 'subjective';

        $rules = [
            'question_text' => ['required', 'string'],
            'marks'         => ['required', 'numeric', 'min:0.25'],
            'order'         => ['nullable', 'integer', 'min:0'],
            'explanation'   => ['nullable', 'string'],
        ];

        if ($type === 'mcq') {
            $rules['options']         = ['required', 'array', 'size:4'];
            $rules['options.*']       = ['required', 'string', 'max:1000'];
            $rules['correct_option']  = ['required', 'integer', 'between:0,3'];
        }

        if ($type === 'subjective') {
            $rules['keywords'] = ['required', 'string'];
        }

        $validated = $request->validate($rules);

        $keywords = null;

        if ($type === 'subjective') {
            $keywords = collect(explode(',', (string) $validated['keywords']))
                ->map(fn (string $k) => trim($k))
                ->filter()
                ->unique()
                ->values()
                ->all();

            if ($keywords === []) {
                return back()
                    ->withErrors(['keywords' => 'Please provide at least one keyword.'])
                    ->withInput();
            }
        }

        $question->update([
            'question_text' => $validated['question_text'],
            'marks'         => $validated['marks'],
            'order'         => $validated['order'] ?? 0,
            'explanation'   => $validated['explanation'] ?? null,
            'keywords'      => $keywords,
        ]);

        if ($type === 'mcq') {
            // Delete old options and recreate to keep it simple
            $question->options()->delete();

            foreach ($validated['options'] as $index => $optionText) {
                QuestionOption::query()->create([
                    'question_id' => $question->id,
                    'option_text' => $optionText,
                    'is_correct'  => (int) $validated['correct_option'] === $index,
                    'order'       => $index + 1,
                ]);
            }
        }

        $question->refresh()->load('options');
        $this->questionBankService->syncFromQuestion($question);

        return redirect()
            ->route('admin.exams.questions.index', $exam)
            ->with('success', 'Question updated successfully.');
    }

    public function destroy(Exam $exam, Question $question): RedirectResponse
    {
        abort_if($question->exam_id !== $exam->id, 404);

        $question->options()->delete();
        $question->delete();

        return redirect()
            ->route('admin.exams.questions.index', $exam)
            ->with('success', 'Question deleted successfully.');
    }

    public function exportExcel(Exam $exam): BinaryFileResponse
    {
        $fileName = 'exam-'.$exam->id.'-questions.xlsx';

        return Excel::download(new ExamQuestionsExport($exam), $fileName);
    }

    public function exportPdf(Exam $exam): Response
    {
        $questions = $exam->questions()
            ->with('options')
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $pdf = Pdf::loadView('admin.questions.export-pdf', [
            'exam'      => $exam,
            'questions' => $questions,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('exam-'.$exam->id.'-questions.pdf');
    }

    public function exportCsv(Exam $exam): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $questions = $exam->questions()
            ->with('options')
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $fileName = 'exam-'.$exam->id.'-questions.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function () use ($questions) {
            $handle = fopen('php://output', 'w');

            // Header row
            fputcsv($handle, ['#', 'Question', 'Type', 'Marks', 'Order', 'Option A', 'Option B', 'Option C', 'Option D', 'Correct Option', 'Keywords', 'Explanation']);

            foreach ($questions as $index => $question) {
                $options = $question->options->sortBy('order')->values();
                $correctIndex = $options->search(fn ($o) => $o->is_correct);
                $correctLabel = $correctIndex !== false ? chr(65 + $correctIndex) : '';

                fputcsv($handle, [
                    $index + 1,
                    $question->question_text,
                    $question->type === 'single_choice' ? 'MCQ' : 'Subjective',
                    $question->marks,
                    $question->order,
                    $options[0]->option_text ?? '',
                    $options[1]->option_text ?? '',
                    $options[2]->option_text ?? '',
                    $options[3]->option_text ?? '',
                    $correctLabel,
                    $question->type === 'short_answer' ? implode(', ', $question->keywords ?? []) : '',
                    $question->explanation ?? '',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function uploadForm(Exam $exam): View
    {
        return view('admin.questions.upload', compact('exam'));
    }

    public function upload(Request $request, Exam $exam): RedirectResponse
    {
        $validated = $request->validate([
            'questions_file' => [
                'required',
                'file',
                'mimes:csv,xls,xlsx',
                'mimetypes:text/plain,text/csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'max:5120',
            ],
        ]);

        $import = new QuestionsImport($exam);

        Excel::import($import, $validated['questions_file']);

        $statusMessage = "{$import->imported} question(s) imported successfully.";

        if ($import->errors !== []) {
            return redirect()
                ->route('admin.exams.questions.index', $exam)
                ->with('success', $statusMessage)
                ->with('warning', 'Some rows were skipped: '.implode(' | ', $import->errors));
        }

        return redirect()
            ->route('admin.exams.questions.index', $exam)
            ->with('success', $statusMessage);
    }

    public function store(Request $request, Exam $exam): RedirectResponse
    {
        $type = $request->input('question_type');

        $rules = [
            'question_type' => ['required', 'in:mcq,subjective'],
            'question_text' => ['required', 'string'],
            'marks'         => ['required', 'numeric', 'min:0.25'],
            'order'         => ['nullable', 'integer', 'min:0'],
            'explanation'   => ['nullable', 'string'],
        ];

        if ($type === 'mcq') {
            $rules['options']        = ['required', 'array', 'size:4'];
            $rules['options.*']      = ['required', 'string', 'max:1000'];
            $rules['correct_option'] = ['required', 'integer', 'between:0,3'];
        }

        if ($type === 'subjective') {
            $rules['keywords'] = ['required', 'string'];
        }

        $validated = $request->validate($rules);

        $keywords = null;

        if ($type === 'subjective') {
            $keywords = collect(explode(',', (string) $validated['keywords']))
                ->map(fn (string $keyword) => trim($keyword))
                ->filter()
                ->unique()
                ->values()
                ->all();

            if ($keywords === []) {
                return back()
                    ->withErrors(['keywords' => 'Please provide at least one keyword.'])
                    ->withInput();
            }
        }

        $question = Question::query()->create([
            'exam_id'       => $exam->id,
            'question_text' => $validated['question_text'],
            'type'          => $type === 'mcq' ? 'single_choice' : 'short_answer',
            'marks'         => $validated['marks'],
            'order'         => $validated['order'] ?? 0,
            'explanation'   => $validated['explanation'] ?? null,
            'keywords'      => $keywords,
        ]);

        if ($type === 'mcq') {
            foreach ($validated['options'] as $index => $optionText) {
                QuestionOption::query()->create([
                    'question_id' => $question->id,
                    'option_text' => $optionText,
                    'is_correct'  => (int) $validated['correct_option'] === $index,
                    'order'       => $index + 1,
                ]);
            }
        }

        $question->load('options');
        $this->questionBankService->syncFromQuestion($question);

        return redirect()
            ->route('admin.exams.questions.index', $exam)
            ->with('success', 'Question added successfully.');
    }

    public function attachFromBank(Exam $exam, QuestionBankQuestion $bankQuestion): RedirectResponse
    {
        $this->questionBankService->cloneToExam($exam, $bankQuestion);

        return redirect()
            ->route('admin.exams.questions.index', $exam)
            ->with('success', 'Question added from question bank.');
    }
}