<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminResultController extends Controller
{
    public function index(): View
    {
        $results = Result::query()
            ->with(['exam:id,title', 'student:id,name,email', 'submission:id,score'])
            ->latest()
            ->paginate(15);

        return view('admin.results.index', compact('results'));
    }

    public function show(Result $result): View
    {
        $result->load([
            'exam:id,title,total_marks',
            'student:id,name,email',
            'submission.answers.question.options',
        ]);

        $answers = $result->submission->answers
            ->sortBy(fn ($a) => $a->question->order ?? $a->question->id)
            ->values();

        return view('admin.results.show', compact('result', 'answers'));
    }

    public function overrideMarks(Request $request, Result $result): RedirectResponse
    {
        $validated = $request->validate([
            'score' => ['required', 'numeric', 'min:0', 'max:'.$result->total_marks],
        ]);

        $score      = round((float) $validated['score'], 2);
        $totalMarks = (float) $result->total_marks;
        $percentage = $totalMarks > 0 ? round(($score / $totalMarks) * 100, 2) : 0.0;

        $result->update([
            'score'      => $score,
            'percentage' => $percentage,
            'passed'     => $percentage >= 50,
        ]);

        $result->submission()->update([
            'score' => $score,
        ]);

        return redirect()
            ->route('admin.results.index')
            ->with('success', 'Result marks overridden successfully.');
    }

    public function togglePublish(Result $result): RedirectResponse
    {
        $result->update([
            'published_at' => $result->published_at ? null : now(),
        ]);

        return redirect()
            ->route('admin.results.index')
            ->with('success', $result->published_at ? 'Result published.' : 'Result unpublished.');
    }
}