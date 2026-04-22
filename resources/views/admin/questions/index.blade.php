<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h1 class="h4 mb-0">Questions - {{ $exam->title }}</h1>
                <div class="text-muted small">Reusable bank + exam-specific questions</div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.exams.questions.export.excel', $exam) }}" class="btn btn-success btn-sm">Export Excel</a>
                <a href="{{ route('admin.exams.questions.export.csv', $exam) }}" class="btn btn-success btn-sm">Export CSV</a>
                <a href="{{ route('admin.exams.questions.export.pdf', $exam) }}" class="btn btn-danger btn-sm">Export PDF</a>
                <a href="{{ route('admin.exams.questions.upload.form', $exam) }}" class="btn btn-info btn-sm text-white">Bulk Upload</a>
                <a href="{{ route('admin.exams.questions.create', $exam) }}" class="btn btn-primary btn-sm">Add Question</a>
            </div>
        </div>
    </x-slot>

    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if (session('warning'))<div class="alert alert-warning">{{ session('warning') }}</div>@endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            @forelse ($questions as $question)
                <div class="border rounded-3 p-3 mb-3 bg-light-subtle">
                    <div class="d-flex justify-content-between gap-3">
                        <div>
                            <div class="fw-semibold">Q{{ $loop->iteration }}. {{ $question->question_text }}</div>
                            <div class="text-muted small d-flex flex-wrap gap-2 mt-1">
                                <span class="badge text-bg-secondary">{{ $question->type === 'short_answer' ? 'Subjective' : 'MCQ' }}</span>
                                <span class="badge text-bg-dark">{{ $question->marks }} marks</span>
                            </div>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('admin.exams.questions.edit', [$exam, $question]) }}" class="btn btn-outline-warning btn-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.exams.questions.destroy', [$exam, $question]) }}" onsubmit="return confirm('Delete this question?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </div>
                    @if ($question->type === 'short_answer')
                        <div class="small mt-2"><strong>Keywords:</strong> {{ implode(', ', $question->keywords ?? []) }}</div>
                    @else
                        <ul class="small mt-2 mb-0">
                            @foreach ($question->options as $option)
                                <li>{{ $option->option_text }} @if ($option->is_correct) <span class="badge text-bg-success">Correct</span> @endif</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @empty
                <div class="text-muted">No questions added yet.</div>
            @endforelse
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">Question Bank</div>
        <div class="card-body">
            @forelse($bankQuestions as $bankQuestion)
                <div class="border rounded-3 p-3 mb-3">
                    <div class="d-flex justify-content-between gap-3">
                        <div>
                            <div class="fw-semibold">{{ $bankQuestion->question_text }}</div>
                            <div class="text-muted small">{{ $bankQuestion->type === 'short_answer' ? 'Subjective' : 'MCQ' }} | {{ $bankQuestion->marks }} marks</div>
                        </div>
                        <form method="POST" action="{{ route('admin.exams.questions.bank.attach', [$exam, $bankQuestion]) }}">
                            @csrf
                            <button class="btn btn-primary btn-sm" type="submit">Add to Exam</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-muted">No question bank entries yet.</div>
            @endforelse
        </div>
    </div>
</x-app-layout>
