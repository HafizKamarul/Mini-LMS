<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\AdminResultController;
use App\Http\Controllers\StudentExamController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('student.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/admin', [DashboardController::class, 'admin'])->name('admin.dashboard');

    Route::resource('admin/exams', ExamController::class)
        ->names('admin.exams');

    Route::get('admin/results', [AdminResultController::class, 'index'])
        ->name('admin.results.index');
    Route::get('admin/results/{result}', [AdminResultController::class, 'show'])
        ->name('admin.results.show');
    Route::patch('admin/results/{result}/override', [AdminResultController::class, 'overrideMarks'])
        ->name('admin.results.override');
    Route::patch('admin/results/{result}/publish-toggle', [AdminResultController::class, 'togglePublish'])
        ->name('admin.results.publish.toggle');

    Route::prefix('admin/exams/{exam}/questions')
        ->name('admin.exams.questions.')
        ->group(function () {
            Route::get('/',               [QuestionController::class, 'index'])->name('index');
            Route::get('/create',         [QuestionController::class, 'create'])->name('create');
            Route::post('/',              [QuestionController::class, 'store'])->name('store');
            Route::get('/{question}/edit',   [QuestionController::class, 'edit'])->name('edit');
            Route::put('/{question}',        [QuestionController::class, 'update'])->name('update');
            Route::delete('/{question}',     [QuestionController::class, 'destroy'])->name('destroy');
            Route::get('/export/excel',   [QuestionController::class, 'exportExcel'])->name('export.excel');
            Route::get('/export/csv', [QuestionController::class, 'exportCsv'])->name('export.csv');
            Route::get('/export/pdf',     [QuestionController::class, 'exportPdf'])->name('export.pdf');
            Route::get('/upload',         [QuestionController::class, 'uploadForm'])->name('upload.form');
            Route::post('/upload',        [QuestionController::class, 'upload'])->name('upload');
            Route::post('/bank/{bankQuestion}/attach', [QuestionController::class, 'attachFromBank'])->name('bank.attach');
        });
});

Route::middleware(['auth', 'verified', 'role:student'])->group(function () {
    Route::get('/student', [DashboardController::class, 'student'])->name('student.dashboard');

    Route::get('/student/exams', [StudentExamController::class, 'index'])->name('student.exams.index');
    Route::post('/student/exams/{exam}/start', [StudentExamController::class, 'start'])->name('student.exams.start');
    Route::get('/student/exams/{exam}/attempt', [StudentExamController::class, 'attempt'])->name('student.exams.attempt');
    Route::post('/student/exams/{exam}/questions/{question}/save', [StudentExamController::class, 'saveAnswer'])->name('student.exams.questions.save');
    Route::post('/student/exams/{exam}/submit', [StudentExamController::class, 'submit'])->name('student.exams.submit');
    Route::get('/student/results/{result}', [StudentExamController::class, 'resultDetail'])->name('student.results.show');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';