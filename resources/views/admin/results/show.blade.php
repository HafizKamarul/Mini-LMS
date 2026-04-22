{{-- resources/views/admin/results/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.results.index') }}"
               class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">← Results</a>
            <span class="text-gray-400">/</span>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Result Detail
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Student info strip --}}
            <div class="mb-6 bg-white rounded-2xl shadow-sm border border-gray-100 px-6 py-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 text-sm font-bold flex-shrink-0">
                    {{ strtoupper(substr($result->student->name ?? 'U', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900">{{ $result->student->name ?? '—' }}</p>
                    <p class="text-xs text-gray-400">{{ $result->student->email ?? '' }}</p>
                </div>
                <div class="flex items-center gap-3 flex-shrink-0">
                    {{-- Override marks form --}}
                    <form method="POST"
                          action="{{ route('admin.results.override', $result) }}"
                          class="flex items-center gap-2"
                          onsubmit="return confirm('Override score for this student?')">
                        @csrf
                        @method('PATCH')
                        <input type="number"
                               name="score"
                               value="{{ $result->score }}"
                               step="0.01" min="0" max="{{ $result->total_marks }}"
                               class="w-24 text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <button type="submit"
                                class="text-xs font-semibold bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg transition-colors">
                            Override
                        </button>
                    </form>
                    {{-- Publish toggle --}}
                    <form method="POST"
                          action="{{ route('admin.results.publish.toggle', $result) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors
                                    {{ $result->published_at
                                        ? 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                                        : 'bg-emerald-600 text-white hover:bg-emerald-700' }}">
                            {{ $result->published_at ? 'Unpublish' : 'Publish' }}
                        </button>
                    </form>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @include('components.result-detail', ['result' => $result, 'answers' => $answers, 'isAdmin' => true])

        </div>
    </div>
</x-app-layout>