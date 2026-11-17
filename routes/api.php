<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProviderDashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public guest routes
    Route::post('/auth/register/client', [AuthController::class, 'registerClient']);
    Route::post('/auth/register/provider', [AuthController::class, 'registerProvider']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Authenticated Sanctum routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/user', [AuthController::class, 'user']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Provider Dashboard API (Spotlight inspired with real DB data)
        Route::prefix('provider')->group(function () {
            Route::get('/dashboard', [ProviderDashboardController::class, 'data']);
            Route::post('/bookings/{id}/status', [ProviderDashboardController::class, 'updateBookingStatus']);
            Route::post('/availability', [ProviderDashboardController::class, 'toggleAvailability']);
            Route::post('/services', [ProviderDashboardController::class, 'createService']);
            Route::post('/profile', [ProviderDashboardController::class, 'updateProfile']);
        });
    });
});

// Legacy / shortcut routes
Route::post('/register/client', [AuthController::class, 'registerClient']);
Route::post('/register/provider', [AuthController::class, 'registerProvider']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
