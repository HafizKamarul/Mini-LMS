<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Questions') }} - {{ $exam->title }}
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.exams.questions.export.excel', $exam) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Export Excel
                </a>
                <a href="{{ route('admin.exams.questions.export.pdf', $exam) }}" class="inline-flex items-center px-4 py-2 bg-rose-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-rose-500 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Export PDF
                </a>
                <a href="{{ route('admin.exams.questions.upload.form', $exam) }}" class="inline-flex items-center px-4 py-2 bg-sky-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Bulk Upload
                </a>
                <a href="{{ route('admin.exams.questions.create', $exam) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Add Question
                </a>
            </div>
        </div>
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

                    @if ($questions->isEmpty())
                        <p class="text-gray-600">No questions added yet.</p>
                    @else
                        <div class="space-y-4">
                            @foreach ($questions as $question)
                                <div class="rounded-lg border border-gray-200 p-4">
                                    <div class="mb-2 flex items-center justify-between">
                                        <p class="font-semibold text-gray-900">Q{{ $loop->iteration }}. {{ $question->question_text }}</p>
                                        <span class="text-sm text-gray-600">{{ $question->marks }} marks</span>
                                    </div>
                                    <p class="mb-2 text-sm text-gray-600">
                                        Type:
                                        {{ $question->type === 'short_answer' ? 'Subjective' : 'MCQ' }}
                                    </p>

                                    @if ($question->type === 'short_answer')
                                        <p class="text-sm text-gray-700">
                                            <span class="font-medium">Keywords:</span>
                                            {{ implode(', ', $question->keywords ?? []) }}
                                        </p>
                                    @else
                                        <ul class="list-disc pl-6 text-sm text-gray-700">
                                            @foreach ($question->options as $option)
                                                <li>
                                                    {{ $option->option_text }}
                                                    @if ($option->is_correct)
                                                        <span class="ml-2 rounded bg-green-100 px-2 py-0.5 text-xs text-green-800">Correct</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-6">
                        <a href="{{ route('admin.exams.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Back to Exams</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
