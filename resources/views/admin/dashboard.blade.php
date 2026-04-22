{{-- resources/views/admin/dashboard.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Admin Dashboard
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Stat Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                {{-- Total Exams --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-5">
                    <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-indigo-50 flex items-center justify-center">
                        <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Exams</p>
                        <p class="text-3xl font-bold text-gray-900 mt-0.5">{{ $totalExams }}</p>
                    </div>
                </div>

                {{-- Total Students --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-5">
                    <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-emerald-50 flex items-center justify-center">
                        <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Students</p>
                        <p class="text-3xl font-bold text-gray-900 mt-0.5">{{ $totalStudents }}</p>
                    </div>
                </div>

                {{-- Ongoing Exams --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-5">
                    <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-amber-50 flex items-center justify-center">
                        <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Ongoing Exams</p>
                        <p class="text-3xl font-bold text-gray-900 mt-0.5">{{ $ongoingExams->count() }}</p>
                    </div>
                </div>

                {{-- Upcoming Exams --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-5">
                    <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-blue-50 flex items-center justify-center">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10m-12 9h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Upcoming Exams</p>
                        <p class="text-3xl font-bold text-gray-900 mt-0.5">{{ $upcomingExams->count() }}</p>
                    </div>
                </div>
            </div>

            {{-- Ongoing Exams Table --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-800">Ongoing Exams</h3>
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        Live
                    </span>
                </div>

                @if($ongoingExams->isEmpty())
                    <div class="px-6 py-10 text-center text-sm text-gray-400">
                        No exams are currently ongoing.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Duration</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ends At</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Submissions</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($ongoingExams as $exam)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $exam->title }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $exam->duration_minutes }} min</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ $exam->ends_at ? $exam->ends_at->format('d M Y, H:i') : '—' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $exam->submissions_count }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('admin.exams.edit', $exam) }}"
                                               class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                                                Manage →
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Upcoming Exams Table --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-800">Upcoming Exams</h3>
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full">
                        Scheduled
                    </span>
                </div>

                @if($upcomingExams->isEmpty())
                    <div class="px-6 py-10 text-center text-sm text-gray-400">
                        No published upcoming exams.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Starts At</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Duration</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Questions</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($upcomingExams as $exam)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $exam->title }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $exam->starts_at?->format('d M Y, H:i') }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $exam->duration_minutes }} min</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $exam->questions_count }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('admin.exams.edit', $exam) }}"
                                               class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                                                Manage →
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Latest Submissions --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-800">Latest Submissions</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Most recent 5 submitted exams</p>
                </div>

                @if($latestSubmissions->isEmpty())
                    <div class="px-6 py-10 text-center text-sm text-gray-400">
                        No submissions yet.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Exam</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Score</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Submitted At</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($latestSubmissions as $submission)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 text-xs font-bold flex-shrink-0">
                                                    {{ strtoupper(substr($submission->student->name ?? 'U', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">{{ $submission->student->name ?? '—' }}</p>
                                                    <p class="text-xs text-gray-400">{{ $submission->student->email ?? '' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ $submission->exam->title ?? '—' }}</td>
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                            {{ $submission->score !== null ? number_format($submission->score, 2) : '—' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $submission->submitted_at ? $submission->submitted_at->format('d M Y, H:i') : '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-3 border-t border-gray-100 text-right">
                        <a href="{{ route('admin.results.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                            View all results →
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>