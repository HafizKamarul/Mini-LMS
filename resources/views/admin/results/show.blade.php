<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.results.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
            <h1 class="h4 mb-0">Result Detail</h1>
        </div>
    </x-slot>

    @include('components.result-detail', ['result' => $result, 'answers' => $answers, 'isAdmin' => true])
</x-app-layout>
