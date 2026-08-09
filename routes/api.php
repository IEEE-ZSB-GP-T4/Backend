<?php

use App\Helpers\ApiResponse;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

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
