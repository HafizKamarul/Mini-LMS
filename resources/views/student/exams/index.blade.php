<x-app-layout>
    <x-slot name="header"><h1 class="h4 mb-0">Active Exams</h1></x-slot>

    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if (session('warning'))<div class="alert alert-warning">{{ session('warning') }}</div>@endif

    <div class="row g-3">
        @forelse($activeExams as $exam)
            @php $submission = $submissions->get($exam->id); @endphp
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <h3 class="h5">{{ $exam->title }}</h3>
                        <p class="text-muted flex-grow-1">{{ $exam->description }}</p>
                        <div class="small text-muted mb-3">Duration: {{ $exam->duration_minutes }} minutes | Questions: {{ $exam->questions_count }} | Ends: {{ $exam->ends_at ? $exam->ends_at->format('M d, Y h:i A') : 'No end time' }}</div>
                        @if ($submission && $submission->status === 'in_progress')
                            <a href="{{ route('student.exams.attempt', $exam) }}" class="btn btn-primary">Resume Exam</a>
                        @else
                            <form method="POST" action="{{ route('student.exams.start', $exam) }}">@csrf<button type="submit" class="btn btn-primary">Start Exam</button></form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="alert alert-info">No active exams available right now.</div></div>
        @endforelse
    </div>
</x-app-layout>
