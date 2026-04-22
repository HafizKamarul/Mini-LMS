{{-- resources/views/student/results/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('student.dashboard') }}"
               class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">← Dashboard</a>
            <span class="text-gray-400">/</span>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Result Detail
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @include('components.result-detail', ['result' => $result, 'answers' => $answers, 'isAdmin' => false])
        </div>
    </div>
</x-app-layout>