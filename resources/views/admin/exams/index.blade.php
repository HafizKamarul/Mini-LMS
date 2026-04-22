<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Exams') }}
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.results.index') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Results
                </a>
                <a href="{{ route('admin.exams.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Create Exam
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

                    @if ($exams->isEmpty())
                        <p class="text-gray-600">No exams found.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Title</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Duration</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Total Marks</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Published</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach ($exams as $exam)
                                        <tr>
                                            <td class="px-4 py-3">{{ $exam->title }}</td>
                                            <td class="px-4 py-3">{{ $exam->duration_minutes }} min</td>
                                            <td class="px-4 py-3">{{ number_format((float) $exam->total_marks, 2) }}</td>
                                            <td class="px-4 py-3">{{ $exam->is_published ? 'Yes' : 'No' }}</td>
                                            <td class="px-4 py-3 text-right space-x-2">
                                                <a href="{{ route('admin.exams.questions.index', $exam) }}" class="text-sky-600 hover:text-sky-900">Questions</a>
                                                <a href="{{ route('admin.exams.edit', $exam) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                                <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Delete this exam?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $exams->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
