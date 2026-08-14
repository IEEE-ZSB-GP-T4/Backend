<?php

use App\Helpers\ApiResponse;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataExportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\StudyPlanController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ===================== Auth Routes =====================

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
});

// ===================== Protected Routes =====================

Route::middleware('auth:sanctum')->group(function () {

    // ===================== User =====================

    Route::get('/user', function (Request $request) {
        return ApiResponse::response(
            200,
            'User retrieved successfully',
            $request->user()
        );
    });

    // ===================== Auth =====================

    Route::post('/logout', [AuthController::class, 'logout']);

    // ===================== Course Routes =====================

    Route::apiResource('courses', CourseController::class);

    // ===================== Task Routes =====================

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

    // ===================== CSV Export Routes =====================

    Route::get(
        '/data-export/all',
        [DataExportController::class, 'downloadAll']
    );

    Route::get(
        '/data-export/users',
        [DataExportController::class, 'users']
    );

    Route::get(
        '/data-export/courses',
        [DataExportController::class, 'courses']
    );

    Route::get(
        '/data-export/study-plans',
        [DataExportController::class, 'studyPlans']
    );

    Route::get(
        '/data-export/tasks',
        [DataExportController::class, 'tasks']
    );

    Route::get(
        '/data-export/notifications',
        [DataExportController::class, 'notifications']
    );

    // ===================== Notification Routes =====================

    Route::get(
        'notifications',
        [NotificationController::class, 'index']
    );

    Route::get(
        'notifications/{notification}',
        [NotificationController::class, 'show']
    );

    Route::patch(
        'notifications/{notification}/read',
        [NotificationController::class, 'markAsRead']
    );

    Route::delete(
        'notifications/{notification}',
        [NotificationController::class, 'destroy']
    );

    // ===================== Study Plan Routes =====================

    Route::get('study-plan/tasks', [StudyPlanController::class, 'tasksForChecklist']);
    Route::post('study-plan', [StudyPlanController::class, 'store']);
    Route::get('study-plan', [StudyPlanController::class, 'index']);
    Route::get('study-plan/history', [StudyPlanController::class, 'history']);
    Route::delete(
        '/study-plan/{studyPlan}',
        [StudyPlanController::class, 'destroy']
    );


    // ===================== Dashboard Routes =====================
    Route::get('/dashboard', [DashboardController::class, 'index']);

});
