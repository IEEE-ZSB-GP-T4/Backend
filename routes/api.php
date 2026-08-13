<?php

use App\Helpers\ApiResponse;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudyPlanController;

// ===================== Auth Routes =====================
Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return ApiResponse::response(
            200,
            'User retrieved successfully',
            $request->user()
        );
    });
    Route::post('/logout', [AuthController::class, 'logout']);
});

// ===================== Course Routes =====================
Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('courses', CourseController::class);

});

// ===================== Task Routes =====================
Route::middleware('auth:sanctum')->group(function () {

    Route::get(
        'tasks/upcoming-deadlines',
        [TaskController::class, 'upcomingDeadlines']
    );

    Route::apiResource('tasks', TaskController::class)
        ->except(['create', 'edit']);

    Route::patch(
        'tasks/{task}/complete',
        [TaskController::class, 'complete']
    );

});

// ===================== Notification Routes =====================
Route::middleware('auth:sanctum')->group(function () {

    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/{notification}', [NotificationController::class, 'show']);
    Route::patch('notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::delete('notifications/{notification}', [NotificationController::class, 'destroy']);

});

// ===================== Study Plan Routes =====================
Route::middleware('auth:sanctum')->group(function () {

    Route::get('study-plan/tasks', [StudyPlanController::class, 'tasksForChecklist']);
    Route::get('study-plan/history', [StudyPlanController::class, 'history']);
    Route::post('study-plan', [StudyPlanController::class, 'store']);
    Route::get('study-plan', [StudyPlanController::class, 'index']);

});
