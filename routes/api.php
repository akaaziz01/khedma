<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProviderDashboardController;
use App\Http\Controllers\Api\PublicProviderController;
use App\Http\Controllers\Api\PublicServiceController;
use App\Http\Controllers\Api\ServiceCategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public guest auth routes
    Route::post('/auth/register/client', [AuthController::class, 'registerClient']);
    Route::post('/auth/register/provider', [AuthController::class, 'registerProvider']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

    // Public guest service discovery & search routes
    Route::get('/services', [PublicServiceController::class, 'index']);
    Route::get('/services/filters', [PublicServiceController::class, 'filters']);
    Route::get('/services/{id}', [PublicServiceController::class, 'show']);

    // Public provider profile for prospective clients & guests
    Route::get('/providers/{id}', [PublicProviderController::class, 'show']);

    // Public category listing (providers & clients browse categories without auth)
    Route::get('/categories', [ServiceCategoryController::class, 'index']);
    Route::get('/categories/{category}', [ServiceCategoryController::class, 'show']);

    // Authenticated Sanctum routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/user', [AuthController::class, 'user']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::put('/profile/password', [AuthController::class, 'updatePassword']);
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
            // Dashboard & user management
            Route::get('/dashboard', [AdminController::class, 'data']);
            Route::get('/users', [AdminController::class, 'listUsers']);
            Route::get('/users/{id}', [AdminController::class, 'showUser']);
            Route::patch('/users/{id}/verify', [AdminController::class, 'verifyUser']);
            Route::patch('/users/{id}/status', [AdminController::class, 'updateUserStatus']);
            Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);

            // Service Categories (full CRUD)
            Route::post('/categories', [ServiceCategoryController::class, 'store']);
            Route::put('/categories/{category}', [ServiceCategoryController::class, 'update']);
            Route::patch('/categories/{category}', [ServiceCategoryController::class, 'update']);
            Route::delete('/categories/{category}', [ServiceCategoryController::class, 'destroy']);

            // Service Moderation
            Route::get('/services', [AdminController::class, 'listServices']);
            Route::post('/services/{id}/moderate', [AdminController::class, 'moderateService']);
            Route::patch('/services/{id}/active', [AdminController::class, 'toggleServiceActive']);
            Route::delete('/services/{id}', [AdminController::class, 'deleteService']);
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
