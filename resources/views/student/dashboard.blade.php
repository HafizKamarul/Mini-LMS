{{-- resources/views/student/dashboard.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            My Dashboard
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Welcome + Avg Score Banner --}}
            <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 rounded-2xl p-6 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <p class="text-indigo-200 text-sm font-medium">Welcome back,</p>
                    <h3 class="text-2xl font-bold mt-0.5">{{ auth()->user()->name }}</h3>
                    <p class="text-indigo-200 text-sm mt-1">
                        {{ $completedExams->count() }} exam{{ $completedExams->count() === 1 ? '' : 's' }} completed
                    </p>
                </div>
                <div class="bg-white/15 rounded-xl px-6 py-4 text-center min-w-[130px]">
                    <p class="text-indigo-100 text-xs font-medium uppercase tracking-wide">Avg Score</p>
                    @if($averageScore !== null)
                        <p class="text-3xl font-bold mt-1">{{ $averageScore }}<span class="text-lg font-normal text-indigo-200">%</span></p>
                    @else
                        <p class="text-2xl font-bold mt-1 text-indigo-200">—</p>
                    @endif
                </div>
            </div>

            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Available Exams</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $upcomingExams->count() }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Completed</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $completedExams->count() }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-violet-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-violet-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Average Score</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $averageScore !== null ? $averageScore . '%' : '—' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Available Exams --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">Available Exams</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Exams you haven't submitted yet</p>
                    </div>
                    @if($upcomingExams->isNotEmpty())
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                            Open
                        </span>
                    @endif
                </div>

                @if($upcomingExams->isEmpty())
                    <div class="px-6 py-12 text-center">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm text-gray-400">No exams available at the moment.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($upcomingExams as $exam)
                            <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $exam->title }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ $exam->questions_count }} question{{ $exam->questions_count === 1 ? '' : 's' }}
                                            &bull; {{ $exam->duration_minutes }} min
                                            &bull; {{ number_format($exam->total_marks, 0) }} marks
                                            @if($exam->ends_at)
                                                &bull; Closes {{ $exam->ends_at->format('d M, H:i') }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <form action="{{ route('student.exams.start', $exam) }}" method="POST" class="flex-shrink-0 ml-4">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition-colors">
                                        Start
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Completed Exams --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-800">Completed Exams</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Your submitted exam history</p>
                </div>

                @if($completedExams->isEmpty())
                    <div class="px-6 py-12 text-center">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-gray-400">You haven't completed any exams yet.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Exam</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Score</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Result</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Submitted</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($completedExams as $submission)
                                    @php $result = $submission->result; @endphp
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-medium text-gray-900">{{ $submission->exam->title ?? '—' }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            @if($result && $result->published_at)
                                                <span class="font-semibold text-gray-900">{{ number_format($result->score, 2) }}</span>
                                                <span class="text-gray-400">/ {{ number_format($result->total_marks, 2) }}</span>
                                                <span class="ml-1.5 text-xs text-gray-500">({{ $result->percentage }}%)</span>
                                            @else
                                                <span class="text-xs text-gray-400 italic">Pending release</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($result && $result->published_at)
                                                @if($result->passed)
                                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                        Passed
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-red-600 bg-red-50 px-2.5 py-1 rounded-full">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                                        Failed
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-xs text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $submission->submitted_at ? $submission->submitted_at->format('d M Y, H:i') : '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>