<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">Exams</h1>
            <div class="d-flex gap-2">
                <a class="btn btn-success" href="{{ route('admin.results.index') }}">Results</a>
                <a class="btn btn-primary" href="{{ route('admin.exams.create') }}">Create Exam</a>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead><tr><th>Title</th><th>Duration</th><th>Total Marks</th><th>Published</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                @forelse($exams as $exam)
                    <tr>
                        <td>{{ $exam->title }}</td>
                        <td>{{ $exam->duration_minutes }} min</td>
                        <td>{{ number_format((float) $exam->total_marks, 2) }}</td>
                        <td>{!! $exam->is_published ? '<span class="badge text-bg-success">Yes</span>' : '<span class="badge text-bg-secondary">No</span>' !!}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-info" href="{{ route('admin.exams.questions.index', $exam) }}">Questions</a>
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.exams.edit', $exam) }}">Edit</a>
                            <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this exam?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">No exams found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $exams->links() }}</div>
</x-app-layout>
