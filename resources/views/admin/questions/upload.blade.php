<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bulk Upload Questions') }} - {{ $exam->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="mb-4 text-sm text-gray-600">
                        Upload a CSV/Excel file with these columns: <strong>question, type, option1, option2, option3, option4, correct_answer, keywords</strong>.
                    </p>
                    <p class="mb-6 text-xs text-gray-500">
                        Use type <strong>mcq</strong> or <strong>subjective</strong>. For mcq, provide option1-4 and correct_answer (1-4 or exact option text). For subjective, provide comma-separated keywords.
                    </p>

                    <form method="POST" action="{{ route('admin.exams.questions.upload', $exam) }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="questions_file" :value="__('Questions File (CSV/XLS/XLSX)')" />
                            <input id="questions_file" name="questions_file" type="file" accept=".csv,.xls,.xlsx" class="mt-1 block w-full text-sm text-gray-700" required>
                            <x-input-error class="mt-2" :messages="$errors->get('questions_file')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Upload & Import') }}</x-primary-button>
                            <a href="{{ route('admin.exams.questions.index', $exam) }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
