<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProviderDashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public guest auth routes
    Route::post('/auth/register/client', [AuthController::class, 'registerClient']);
    Route::post('/auth/register/provider', [AuthController::class, 'registerProvider']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

    // Authenticated Sanctum routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/user', [AuthController::class, 'user']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::post('/auth/logout-all', [AuthController::class, 'logoutAll']);

        // Provider Dashboard API (Spotlight inspired with real DB data)
        Route::prefix('provider')->group(function () {
            Route::get('/dashboard', [ProviderDashboardController::class, 'data']);
            Route::post('/bookings/{id}/status', [ProviderDashboardController::class, 'updateBookingStatus']);
            Route::post('/availability', [ProviderDashboardController::class, 'toggleAvailability']);
            Route::post('/services', [ProviderDashboardController::class, 'createService']);
            Route::post('/profile', [ProviderDashboardController::class, 'updateProfile']);
        });

        // Admin Management Area (Secured with Admin Middleware)
        Route::middleware('admin')->prefix('admin')->group(function () {
            Route::get('/dashboard', [AdminController::class, 'data']);
            Route::patch('/users/{id}/status', [AdminController::class, 'updateUserStatus']);
            Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
        });
    });
});

// Legacy / shortcut routes
Route::post('/register/client', [AuthController::class, 'registerClient']);
Route::post('/register/provider', [AuthController::class, 'registerProvider']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
