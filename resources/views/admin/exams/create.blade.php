<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Exam') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.exams.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div>
                            <x-input-label for="duration_minutes" :value="__('Duration (minutes)')" />
                            <x-text-input id="duration_minutes" name="duration_minutes" type="number" min="1" class="mt-1 block w-full" :value="old('duration_minutes', 60)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('duration_minutes')" />
                        </div>

                        <div>
                            <x-input-label for="total_marks" :value="__('Total Marks')" />
                            <x-text-input id="total_marks" name="total_marks" type="number" min="0" step="0.01" class="mt-1 block w-full" :value="old('total_marks', 100)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('total_marks')" />
                        </div>

                        <div>
                            <x-input-label for="starts_at" :value="__('Starts At')" />
                            <x-text-input id="starts_at" name="starts_at" type="datetime-local" class="mt-1 block w-full" :value="old('starts_at')" />
                            <x-input-error class="mt-2" :messages="$errors->get('starts_at')" />
                        </div>

                        <div>
                            <x-input-label for="ends_at" :value="__('Ends At')" />
                            <x-text-input id="ends_at" name="ends_at" type="datetime-local" class="mt-1 block w-full" :value="old('ends_at')" />
                            <x-input-error class="mt-2" :messages="$errors->get('ends_at')" />
                        </div>

                        <div class="flex items-center">
                            <input id="is_published" name="is_published" type="checkbox" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('is_published'))>
                            <label for="is_published" class="ml-2 text-sm text-gray-600">Publish exam immediately</label>
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('is_published')" />

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Save Exam') }}</x-primary-button>
                            <a href="{{ route('admin.exams.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
