<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $exams = Exam::query()
            ->latest()
            ->paginate(10);

        return view('admin.exams.index', compact('exams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.exams.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'total_marks' => ['required', 'numeric', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $validated['created_by'] = $request->user()->id;
        $validated['is_published'] = $request->boolean('is_published');

        Exam::query()->create($validated);

        return redirect()
            ->route('admin.exams.index')
            ->with('success', 'Exam created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Exam $exam): RedirectResponse
    {
        return redirect()->route('admin.exams.edit', $exam);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exam $exam): View
    {
        return view('admin.exams.edit', compact('exam'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Exam $exam): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'total_marks' => ['required', 'numeric', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        $exam->update($validated);

        return redirect()
            ->route('admin.exams.index')
            ->with('success', 'Exam updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam): RedirectResponse
    {
        $exam->delete();

        return redirect()
            ->route('admin.exams.index')
            ->with('success', 'Exam deleted successfully.');
    }
}
