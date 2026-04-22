<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Question') }} - {{ $exam->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.exams.questions.store', $exam) }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="question_type" :value="__('Question Type')" />
                            <select id="question_type" name="question_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="mcq" @selected(old('question_type', 'mcq') === 'mcq')>MCQ</option>
                                <option value="subjective" @selected(old('question_type') === 'subjective')>Subjective</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('question_type')" />
                        </div>

                        <div>
                            <x-input-label for="question_text" :value="__('Question Text')" />
                            <textarea id="question_text" name="question_text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('question_text') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('question_text')" />
                        </div>

                        <div>
                            <x-input-label for="marks" :value="__('Marks')" />
                            <x-text-input id="marks" name="marks" type="number" min="0.25" step="0.25" class="mt-1 block w-full" :value="old('marks', 1)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('marks')" />
                        </div>

                        <div>
                            <x-input-label for="order" :value="__('Display Order')" />
                            <x-text-input id="order" name="order" type="number" min="0" class="mt-1 block w-full" :value="old('order', 0)" />
                            <x-input-error class="mt-2" :messages="$errors->get('order')" />
                        </div>

                        <div>
                            <x-input-label for="explanation" :value="__('Explanation (optional)')" />
                            <textarea id="explanation" name="explanation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('explanation') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('explanation')" />
                        </div>

                        <div id="mcq-fields" class="space-y-4">
                            <p class="text-sm font-medium text-gray-700">MCQ Options (exactly 4)</p>
                            @for ($i = 0; $i < 4; $i++)
                                <div>
                                    <x-input-label :for="'option_'.$i" :value="__('Option '.($i + 1))" />
                                    <x-text-input :id="'option_'.$i" name="options[]" type="text" class="mt-1 block w-full" :value="old('options.'.$i)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('options.'.$i)" />
                                </div>
                            @endfor

                            <div>
                                <x-input-label for="correct_option" :value="__('Correct Option')" />
                                <select id="correct_option" name="correct_option" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select correct option</option>
                                    <option value="0" @selected(old('correct_option') === '0')>Option 1</option>
                                    <option value="1" @selected(old('correct_option') === '1')>Option 2</option>
                                    <option value="2" @selected(old('correct_option') === '2')>Option 3</option>
                                    <option value="3" @selected(old('correct_option') === '3')>Option 4</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('correct_option')" />
                            </div>
                        </div>

                        <div id="subjective-fields" class="space-y-2">
                            <x-input-label for="keywords" :value="__('Keywords (comma separated)')" />
                            <x-text-input id="keywords" name="keywords" type="text" class="mt-1 block w-full" :value="old('keywords')" />
                            <p class="text-xs text-gray-500">Example: polymorphism, inheritance, encapsulation</p>
                            <x-input-error class="mt-2" :messages="$errors->get('keywords')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Save Question') }}</x-primary-button>
                            <a href="{{ route('admin.exams.questions.index', $exam) }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const typeSelect = document.getElementById('question_type');
            const mcqFields = document.getElementById('mcq-fields');
            const subjectiveFields = document.getElementById('subjective-fields');

            function toggleFields() {
                const isMcq = typeSelect.value === 'mcq';
                mcqFields.style.display = isMcq ? 'block' : 'none';
                subjectiveFields.style.display = isMcq ? 'none' : 'block';
            }

            typeSelect.addEventListener('change', toggleFields);
            toggleFields();
        });
    </script>
</x-app-layout>
