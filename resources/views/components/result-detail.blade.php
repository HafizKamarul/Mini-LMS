<section class="space-y-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3 text-white {{ $result->passed ? 'bg-success' : 'bg-danger' }} rounded">
            <div>
                <div class="small opacity-75">{{ $isAdmin ? ($result->student->name ?? 'Student') : 'Your Result' }}</div>
                <h2 class="h4 mb-1">{{ $result->exam->title ?? 'Exam' }}</h2>
                <div class="small opacity-75">Submitted @if($result->submission?->submitted_at) {{ $result->submission->submitted_at->format('d M Y, H:i') }} @endif @if($result->time_taken) | Time taken: {{ gmdate('H:i:s', $result->time_taken) }} @endif</div>
            </div>
            <div class="text-center bg-white bg-opacity-25 rounded p-3">
                <div class="small text-uppercase">Score</div>
                <div class="h3 mb-0">{{ number_format($result->score, 2) }} / {{ number_format($result->total_marks, 2) }}</div>
                <div class="fw-semibold">{{ $result->percentage }}%</div>
                <div class="small fw-bold">{{ $result->passed ? 'Passed' : 'Failed' }}</div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white"><strong>Answer Breakdown</strong> <span class="text-muted small">{{ $answers->count() }} question(s)</span></div>
        <div class="card-body p-0">
            @forelse($answers as $i => $answer)
                @php $question = $answer->question; $correct = $answer->is_correct; $skipped = ! $answer->question_option_id && ! $answer->answer_text; @endphp
                <div class="p-3 border-bottom {{ $correct ? 'bg-success-subtle' : ($skipped ? 'bg-light' : 'bg-danger-subtle') }}">
                    <div class="d-flex justify-content-between gap-3">
                        <div><div class="small text-muted">Q{{ $i + 1 }} | {{ $question->type === 'single_choice' ? 'MCQ' : 'Short Answer' }} | {{ number_format((float)$question->marks, 2) }} mark(s)</div><div class="fw-semibold">{{ $question->question_text }}</div></div>
                        <div class="text-end">
                            @if($skipped)<span class="badge text-bg-secondary">Not attempted</span>@elseif($correct)<span class="badge text-bg-success">+{{ number_format((float)$answer->marks_awarded, 2) }}</span>@else<span class="badge text-bg-danger">0 / {{ number_format((float)$question->marks, 2) }}</span>@endif
                        </div>
                    </div>

                    @if($question->type === 'single_choice')
                        <div class="list-group mt-3">
                            @foreach($question->options->sortBy('order') as $option)
                                @php $wasSelected = (int)$answer->question_option_id === (int)$option->id; $isCorrectOpt = $option->is_correct; @endphp
                                <div class="list-group-item {{ $isCorrectOpt ? 'list-group-item-success' : ($wasSelected && !$isCorrectOpt ? 'list-group-item-danger' : '') }}">{{ $option->option_text }} @if($isCorrectOpt) <span class="badge text-bg-success float-end">Correct answer</span> @endif @if($wasSelected && !$isCorrectOpt) <span class="badge text-bg-danger float-end">Your answer</span> @endif</div>
                            @endforeach
                        </div>
                    @endif

                    @if($question->type === 'short_answer')
                        <div class="mt-3"><div class="card"><div class="card-body"><div class="small text-muted">Your answer</div><div>{{ $answer->answer_text ?? '—' }}</div></div></div></div>
                    @endif

                    @if($question->explanation)
                        <div class="alert alert-info mt-3 mb-0"><strong>Explanation:</strong> {{ $question->explanation }}</div>
                    @endif
                </div>
            @empty
                <div class="p-4 text-center text-muted">No answers recorded for this submission.</div>
            @endforelse
        </div>
    </div>
</section>
