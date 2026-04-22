{{-- resources/views/admin/questions/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.exams.questions.index', $exam) }}"
               class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">← Questions</a>
            <span class="text-gray-400">/</span>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Question</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

                @if ($errors->any())
                    <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            <p>• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST"
                      action="{{ route('admin.exams.questions.update', [$exam, $question]) }}">
                    @csrf
                    @method('PUT')

                    {{-- Question type badge (read-only) --}}
                    <div class="mb-6">
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1 rounded-full
                            {{ $question->type === 'single_choice' ? 'bg-indigo-50 text-indigo-700' : 'bg-violet-50 text-violet-700' }}">
                            {{ $question->type === 'single_choice' ? 'Multiple Choice (MCQ)' : 'Subjective / Short Answer' }}
                        </span>
                        <p class="mt-1 text-xs text-gray-400">Question type cannot be changed after creation.</p>
                    </div>

                    {{-- Question Text --}}
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="question_text">
                            Question Text <span class="text-red-500">*</span>
                        </label>
                        <textarea id="question_text" name="question_text" rows="3"
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                  required>{{ old('question_text', $question->question_text) }}</textarea>
                    </div>

                    {{-- Marks & Order --}}
                    <div class="grid grid-cols-2 gap-4 mb-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="marks">
                                Marks <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="marks" name="marks" step="0.25" min="0.25"
                                   value="{{ old('marks', $question->marks) }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="order">
                                Order
                            </label>
                            <input type="number" id="order" name="order" min="0"
                                   value="{{ old('order', $question->order) }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                    </div>

                    {{-- MCQ Options --}}
                    @if ($question->type === 'single_choice')
                        @php
                            $options = $question->options->sortBy('order')->values();
                            $correctIndex = $options->search(fn($o) => $o->is_correct);
                        @endphp
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Options <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-400 font-normal ml-1">(select the correct answer)</span>
                            </label>
                            <div class="space-y-3">
                                @foreach ($options as $i => $option)
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="correct_option" value="{{ $i }}"
                                               id="correct_{{ $i }}"
                                               {{ old('correct_option', $correctIndex) == $i ? 'checked' : '' }}
                                               class="text-indigo-600 focus:ring-indigo-500">
                                        <input type="text" name="options[{{ $i }}]"
                                               value="{{ old("options.$i", $option->option_text) }}"
                                               placeholder="Option {{ chr(65 + $i) }}"
                                               class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                               required>
                                        <label for="correct_{{ $i }}"
                                               class="text-xs text-gray-500 w-16 text-center cursor-pointer">
                                            Correct
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Subjective Keywords --}}
                    @if ($question->type === 'short_answer')
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="keywords">
                                Keywords <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-400 font-normal ml-1">(comma-separated; 50 % must match to award marks)</span>
                            </label>
                            <input type="text" id="keywords" name="keywords"
                                   value="{{ old('keywords', implode(', ', $question->keywords ?? [])) }}"
                                   placeholder="e.g. photosynthesis, chlorophyll, sunlight"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                   required>
                        </div>
                    @endif

                    {{-- Explanation --}}
                    <div class="mb-7">
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="explanation">
                            Explanation
                            <span class="text-xs text-gray-400 font-normal ml-1">(shown after grading)</span>
                        </label>
                        <textarea id="explanation" name="explanation" rows="3"
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                  placeholder="Optional explanation for the correct answer...">{{ old('explanation', $question->explanation) }}</textarea>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit"
                                class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors">
                            Save Changes
                        </button>
                        <a href="{{ route('admin.exams.questions.index', $exam) }}"
                           class="text-sm text-gray-500 hover:text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>