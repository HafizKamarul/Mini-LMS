<?php

namespace App\Http\Controllers;

use App\Exports\ExamQuestionsExport;
use App\Imports\QuestionsImport;
use App\Models\Exam;
use App\Models\Question;
use App\Models\QuestionOption;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class QuestionController extends Controller
{
    public function index(Exam $exam): View
    {
        $questions = $exam->questions()
            ->with('options')
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return view('admin.questions.index', compact('exam', 'questions'));
    }

    public function create(Exam $exam): View
    {
        return view('admin.questions.create', compact('exam'));
    }

    public function exportExcel(Exam $exam): BinaryFileResponse
    {
        $fileName = 'exam-'.$exam->id.'-questions.xlsx';

        return Excel::download(new ExamQuestionsExport($exam), $fileName);
    }

    public function exportPdf(Exam $exam): BinaryFileResponse
    {
        $questions = $exam->questions()
            ->with('options')
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $pdf = Pdf::loadView('admin.questions.export-pdf', [
            'exam' => $exam,
            'questions' => $questions,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('exam-'.$exam->id.'-questions.pdf');
    }

    public function uploadForm(Exam $exam): View
    {
        return view('admin.questions.upload', compact('exam'));
    }

    public function upload(Request $request, Exam $exam): RedirectResponse
    {
        $validated = $request->validate([
            'questions_file' => ['required', 'file', 'mimes:csv,xls,xlsx'],
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
            'marks' => ['required', 'numeric', 'min:0.25'],
            'order' => ['nullable', 'integer', 'min:0'],
            'explanation' => ['nullable', 'string'],
        ];

        if ($type === 'mcq') {
            $rules['options'] = ['required', 'array', 'size:4'];
            $rules['options.*'] = ['required', 'string', 'max:1000'];
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
            'exam_id' => $exam->id,
            'question_text' => $validated['question_text'],
            'type' => $type === 'mcq' ? 'single_choice' : 'short_answer',
            'marks' => $validated['marks'],
            'order' => $validated['order'] ?? 0,
            'explanation' => $validated['explanation'] ?? null,
            'keywords' => $keywords,
        ]);

        if ($type === 'mcq') {
            foreach ($validated['options'] as $index => $optionText) {
                QuestionOption::query()->create([
                    'question_id' => $question->id,
                    'option_text' => $optionText,
                    'is_correct' => (int) $validated['correct_option'] === $index,
                    'order' => $index + 1,
                ]);
            }
        }

        return redirect()
            ->route('admin.exams.questions.index', $exam)
            ->with('success', 'Question added successfully.');
    }
}
