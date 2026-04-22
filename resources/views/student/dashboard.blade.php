<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h1 class="h4 mb-0">Student Dashboard</h1>
            <a href="{{ route('student.exams.index') }}" class="btn btn-primary btn-sm">Go to Active Exams</a>
        </div>
    </x-slot>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Available Exams</div><div class="h3 mb-0">{{ $availableExams->count() }}</div></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Upcoming Exams</div><div class="h3 mb-0">{{ $upcomingExams->count() }}</div></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Completed</div><div class="h3 mb-0">{{ $completedExams->count() }}</div></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Average Score</div><div class="h3 mb-0">{{ $averageScore !== null ? $averageScore . '%' : '-' }}</div></div></div></div>
    </div>

    <div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white">Available Exams</div><div class="card-body">
        @forelse($availableExams as $exam)
            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                <div>
                    <div class="fw-semibold">{{ $exam->title }}</div>
                    <div class="small text-muted">{{ $exam->questions_count }} questions | {{ $exam->duration_minutes }} min</div>
                </div>
                <form method="POST" action="{{ route('student.exams.start', $exam) }}">@csrf<button class="btn btn-primary btn-sm" type="submit">Start</button></form>
            </div>
        @empty
            <div class="text-muted">No exams available right now.</div>
        @endforelse
    </div></div>

    <div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white">Upcoming Exams</div><div class="card-body">
        @forelse($upcomingExams as $exam)
            <div class="border-bottom py-2">
                <div class="fw-semibold">{{ $exam->title }}</div>
                <div class="small text-muted">Starts {{ $exam->starts_at?->format('d M Y H:i') }} | {{ $exam->questions_count }} questions</div>
            </div>
        @empty
            <div class="text-muted">No upcoming exams.</div>
        @endforelse
    </div></div>

    <div class="card border-0 shadow-sm"><div class="card-header bg-white">Completed Exams</div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Exam</th><th>Score</th><th>Status</th><th>Submitted</th><th></th></tr></thead><tbody>
        @forelse($completedExams as $submission)
            @php $result = $submission->result; @endphp
            <tr>
                <td>{{ $submission->exam->title ?? '-' }}</td>
                <td>@if($result && $result->published_at) {{ number_format($result->score, 2) }} / {{ number_format($result->total_marks, 2) }} @else Pending @endif</td>
                <td>{{ $result && $result->published_at ? ($result->passed ? 'Passed' : 'Failed') : 'Pending release' }}</td>
                <td>{{ $submission->submitted_at ? $submission->submitted_at->format('d M Y H:i') : '-' }}</td>
                <td>@if($result && $result->published_at && $result->id)<a href="{{ route('student.results.show', ['result' => $result->id]) }}" class="btn btn-sm btn-outline-primary">View</a>@endif</td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center text-muted">No completed exams yet.</td></tr>
        @endforelse
    </tbody></table></div></div>
</x-app-layout>
