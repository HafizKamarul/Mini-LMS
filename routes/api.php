<?php

use App\Http\Controllers\Api\V1\AuthTokenController;
use App\Http\Controllers\Api\V1\ExamApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/login', [AuthTokenController::class, 'store']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/logout', [AuthTokenController::class, 'destroy']);

        Route::get('/exams', [ExamApiController::class, 'index']);
        Route::post('/results/{exam_id}', [ExamApiController::class, 'storeResult']);
        Route::get('/student/{id}/transcript', [ExamApiController::class, 'transcript']);
    });
});