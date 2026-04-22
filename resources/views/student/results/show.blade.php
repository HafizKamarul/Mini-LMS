<x-app-layout>
    <x-slot name="header"><h1 class="h4 mb-0">My Result</h1></x-slot>
    @include('components.result-detail', ['result' => $result, 'answers' => $answers, 'isAdmin' => false])
</x-app-layout>
