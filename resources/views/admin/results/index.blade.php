<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Results') }}
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

                    @if ($results->isEmpty())
                        <p class="text-gray-600">No results found.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Exam</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Student</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Score</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Percentage</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Time Taken</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Published</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach ($results as $result)
                                        @php
                                            $minutes = intdiv((int) $result->time_taken, 60);
                                            $seconds = (int) $result->time_taken % 60;
                                        @endphp
                                        <tr>
                                            <td class="px-4 py-3">{{ $result->exam->title }}</td>
                                            <td class="px-4 py-3">
                                                {{ $result->student->name }}<br>
                                                <span class="text-xs text-gray-500">{{ $result->student->email }}</span>
                                            </td>
                                            <td class="px-4 py-3">{{ number_format((float) $result->score, 2) }} / {{ number_format((float) $result->total_marks, 2) }}</td>
                                            <td class="px-4 py-3">{{ number_format((float) $result->percentage, 2) }}%</td>
                                            <td class="px-4 py-3">{{ sprintf('%02d:%02d', $minutes, $seconds) }}</td>
                                            <td class="px-4 py-3">{{ $result->published_at ? 'Yes' : 'No' }}</td>
                                            <td class="px-4 py-3 text-right space-y-2">
                                                <form action="{{ route('admin.results.override', $result) }}" method="POST" class="flex justify-end items-center gap-2">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="number" name="score" min="0" max="{{ $result->total_marks }}" step="0.01" value="{{ $result->score }}" class="w-24 rounded-md border-gray-300 text-sm" required>
                                                    <button type="submit" class="text-indigo-600 hover:text-indigo-900 text-sm">Override</button>
                                                </form>

                                                <form action="{{ route('admin.results.publish.toggle', $result) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-sm {{ $result->published_at ? 'text-amber-600 hover:text-amber-800' : 'text-emerald-600 hover:text-emerald-800' }}">
                                                        {{ $result->published_at ? 'Unpublish' : 'Publish' }}
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $results->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
