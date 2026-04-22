<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">Admin Dashboard</h1>
    </x-slot>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted small">Total Exams</div><div class="h3 mb-0">{{ $totalExams }}</div></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted small">Total Students</div><div class="h3 mb-0">{{ $totalStudents }}</div></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted small">Ongoing Exams</div><div class="h3 mb-0">{{ $ongoingExams->count() }}</div></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted small">Upcoming Exams</div><div class="h3 mb-0">{{ $upcomingExams->count() }}</div></div></div></div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Ongoing Exams</div>
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead><tr><th>Title</th><th>Duration</th><th>Ends At</th><th>Submissions</th><th></th></tr></thead>
                <tbody>
                @forelse($ongoingExams as $exam)
                    <tr>
                        <td>{{ $exam->title }}</td>
                        <td>{{ $exam->duration_minutes }} min</td>
                        <td>{{ $exam->ends_at ? $exam->ends_at->format('d M Y H:i') : 'N/A' }}</td>
                        <td>{{ $exam->submissions_count }}</td>
                        <td><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.exams.edit', $exam) }}">Manage</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">No ongoing exams.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Upcoming Exams</div>
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead><tr><th>Title</th><th>Starts At</th><th>Duration</th><th>Questions</th></tr></thead>
                <tbody>
                @forelse($upcomingExams as $exam)
                    <tr>
                        <td>{{ $exam->title }}</td>
                        <td>{{ $exam->starts_at?->format('d M Y H:i') }}</td>
                        <td>{{ $exam->duration_minutes }} min</td>
                        <td>{{ $exam->questions_count }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted">No upcoming exams.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Latest Submissions</div>
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead><tr><th>Student</th><th>Exam</th><th>Score</th><th>Submitted At</th></tr></thead>
                <tbody>
                @forelse($latestSubmissions as $submission)
                    <tr>
                        <td>{{ $submission->student->name ?? 'N/A' }}<div class="small text-muted">{{ $submission->student->email ?? '' }}</div></td>
                        <td>{{ $submission->exam->title ?? 'N/A' }}</td>
                        <td>{{ $submission->score !== null ? number_format($submission->score, 2) : 'N/A' }}</td>
                        <td>{{ $submission->submitted_at ? $submission->submitted_at->format('d M Y H:i') : 'N/A' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted">No submissions yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
