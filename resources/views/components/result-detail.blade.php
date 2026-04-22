{{--
    resources/views/components/result-detail.blade.php
    Props: $result, $answers, $isAdmin (bool)
--}}
<div class="space-y-6">

    {{-- Summary Banner --}}
    <div class="rounded-2xl p-6
        {{ $result->passed ? 'bg-emerald-600' : 'bg-red-500' }}
        text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <p class="text-sm font-medium opacity-80">
                {{ $isAdmin ? ($result->student->name ?? 'Student') : 'Your Result' }}
            </p>
            <h3 class="text-2xl font-bold mt-0.5">{{ $result->exam->title ?? 'Exam' }}</h3>
            <p class="text-sm opacity-80 mt-1">
                Submitted
                @if($result->submission?->submitted_at)
                    {{ $result->submission->submitted_at->format('d M Y, H:i') }}
                @endif
                @if($result->time_taken)
                    &bull; Time taken: {{ gmdate('H:i:s', $result->time_taken) }}
                @endif
            </p>
        </div>
        <div class="bg-white/20 rounded-xl px-6 py-4 text-center min-w-[140px]">
            <p class="text-xs font-medium uppercase tracking-wide opacity-80">Score</p>
            <p class="text-3xl font-bold mt-1">
                {{ number_format($result->score, 2) }}
                <span class="text-lg font-normal opacity-70">/ {{ number_format($result->total_marks, 2) }}</span>
            </p>
            <p class="text-sm font-semibold mt-0.5">{{ $result->percentage }}%</p>
            <p class="text-xs font-bold mt-1 uppercase tracking-wide">
                {{ $result->passed ? '✓ Passed' : '✗ Failed' }}
            </p>
        </div>
    </div>

    {{-- Per-question breakdown --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-800">Answer Breakdown</h3>
            <p class="text-xs text-gray-400 mt-0.5">{{ $answers->count() }} question(s) attempted</p>
        </div>

        @forelse($answers as $i => $answer)
            @php
                $question  = $answer->question;
                $correct   = $answer->is_correct;
                $skipped   = ! $answer->question_option_id && ! $answer->answer_text;
            @endphp

            <div class="px-6 py-5 border-b border-gray-100 last:border-b-0
                {{ $correct ? 'bg-emerald-50/40' : ($skipped ? 'bg-gray-50/60' : 'bg-red-50/40') }}">

                {{-- Question header --}}
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-500 mb-1">
                            Q{{ $i + 1 }}
                            &bull;
                            {{ $question->type === 'single_choice' ? 'MCQ' : 'Short Answer' }}
                            &bull; {{ number_format((float)$question->marks, 2) }} mark(s)
                        </p>
                        <p class="text-sm font-medium text-gray-900">{{ $question->question_text }}</p>
                    </div>
                    <div class="flex-shrink-0 text-right">
                        @if($skipped)
                            <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-500">
                                Not attempted
                            </span>
                        @elseif($correct)
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                +{{ number_format((float)$answer->marks_awarded, 2) }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-red-100 text-red-600">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                                0 / {{ number_format((float)$question->marks, 2) }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- MCQ options --}}
                @if($question->type === 'single_choice')
                    <div class="mt-3 space-y-1.5">
                        @foreach($question->options->sortBy('order') as $option)
                            @php
                                $wasSelected  = (int)$answer->question_option_id === (int)$option->id;
                                $isCorrectOpt = $option->is_correct;
                            @endphp
                            <div class="flex items-center gap-2.5 text-sm px-3 py-2 rounded-lg
                                {{ $isCorrectOpt ? 'bg-emerald-100 text-emerald-800' :
                                   ($wasSelected && !$isCorrectOpt ? 'bg-red-100 text-red-700' : 'text-gray-600') }}">
                                @if($wasSelected)
                                    <svg class="w-4 h-4 flex-shrink-0 {{ $isCorrectOpt ? 'text-emerald-600' : 'text-red-500' }}"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        @if($isCorrectOpt)
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        @else
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        @endif
                                    </svg>
                                @elseif($isCorrectOpt)
                                    <svg class="w-4 h-4 flex-shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <span class="w-4 h-4 flex-shrink-0 rounded-full border border-gray-300 inline-block"></span>
                                @endif
                                <span>{{ $option->option_text }}</span>
                                @if($isCorrectOpt)
                                    <span class="ml-auto text-xs font-semibold text-emerald-700">Correct answer</span>
                                @endif
                                @if($wasSelected && !$isCorrectOpt)
                                    <span class="ml-auto text-xs font-semibold text-red-600">Your answer</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Subjective answer --}}
                @if($question->type === 'short_answer')
                    <div class="mt-3 space-y-2">
                        <div class="rounded-lg bg-white border border-gray-200 px-3 py-2">
                            <p class="text-xs text-gray-500 font-medium mb-1">Your answer</p>
                            <p class="text-sm text-gray-800">
                                {{ $answer->answer_text ?? '—' }}
                            </p>
                        </div>
                        @if($isAdmin || !$correct)
                            <div class="rounded-lg bg-emerald-50 border border-emerald-200 px-3 py-2">
                                <p class="text-xs text-emerald-700 font-medium mb-1">Keywords expected</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($question->keywords ?? [] as $kw)
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-medium">
                                            {{ $kw }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Explanation --}}
                @if($question->explanation)
                    <div class="mt-3 rounded-lg bg-blue-50 border border-blue-100 px-3 py-2">
                        <p class="text-xs text-blue-700 font-medium mb-0.5">Explanation</p>
                        <p class="text-sm text-blue-800">{{ $question->explanation }}</p>
                    </div>
                @endif

            </div>
        @empty
            <div class="px-6 py-10 text-center text-sm text-gray-400">
                No answers recorded for this submission.
            </div>
        @endforelse
    </div>
</div>