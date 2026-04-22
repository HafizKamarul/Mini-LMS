<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Result;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function admin(): View
    {
        $now = now();

        $totalExams = Exam::query()->count();

        $totalStudents = User::query()->where('role', 'student')->count();

        $ongoingExams = Exam::query()
            ->where('is_published', true)
            ->where(function ($query) use ($now) {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->withCount('submissions')
            ->get();

        $latestSubmissions = Submission::query()
            ->with(['student:id,name,email', 'exam:id,title'])
            ->latest('submitted_at')
            ->whereNotNull('submitted_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalExams',
            'totalStudents',
            'ongoingExams',
            'latestSubmissions'
        ));
    }

    public function student(Request $request): View
    {
        $now = now();
        $student = $request->user();

        $upcomingExams = Exam::query()
            ->where('is_published', true)
            ->where(function ($query) use ($now) {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->whereDoesntHave('submissions', function ($query) use ($student) {
                $query->where('student_id', $student->id)
                      ->where('status', 'submitted');
            })
            ->withCount('questions')
            ->orderBy('starts_at')
            ->get();

        $completedExams = Submission::query()
            ->where('student_id', $student->id)
            ->where('status', 'submitted')
            ->with(['exam:id,title,total_marks', 'result:submission_id,score,total_marks,percentage,passed,published_at'])
            ->latest('submitted_at')
            ->get();

        $averageScore = Result::query()
            ->where('student_id', $student->id)
            ->avg('percentage');

        $averageScore = $averageScore ? round($averageScore, 1) : null;

        return view('student.dashboard', compact(
            'upcomingExams',
            'completedExams',
            'averageScore'
        ));
    }
}