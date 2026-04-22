<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Active Exams') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="mb-4 rounded-md bg-green-100 p-3 text-green-800">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="mb-4 rounded-md bg-yellow-100 p-3 text-yellow-800">
                            {{ session('warning') }}
                        </div>
                    @endif

                    @if ($activeExams->isEmpty())
                        <p class="text-gray-600">No active exams available right now.</p>
                    @else
                        <div class="grid gap-4 md:grid-cols-2">
                            @foreach ($activeExams as $exam)
                                @php
                                    $submission = $submissions->get($exam->id);
                                @endphp
                                <div class="rounded-lg border border-gray-200 p-5">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $exam->title }}</h3>
                                    <p class="mt-1 text-sm text-gray-600">{{ $exam->description }}</p>

                                    <div class="mt-4 space-y-1 text-sm text-gray-700">
                                        <p>Duration: {{ $exam->duration_minutes }} minutes</p>
                                        <p>Questions: {{ $exam->questions_count }}</p>
                                        <p>Ends: {{ $exam->ends_at ? $exam->ends_at->format('M d, Y h:i A') : 'No end time' }}</p>
                                    </div>

                                    <div class="mt-5">
                                        @if ($submission && $submission->status === 'in_progress')
                                            <a href="{{ route('student.exams.attempt', $exam) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                                                Resume Exam
                                            </a>
                                        @else
                                            <form method="POST" action="{{ route('student.exams.start', $exam) }}">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                                                    Start Exam
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
