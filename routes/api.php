<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ClientFavoriteController;
use App\Http\Controllers\Api\JobRequestController;
use App\Http\Controllers\Api\ProposalController;
use App\Http\Controllers\Api\ProviderCompareController;
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

    // Public provider discovery, search & side-by-side comparison
    Route::get('/providers', [PublicProviderController::class, 'index']);
    Route::get('/providers/filters', [PublicProviderController::class, 'filters']);
    Route::get('/providers/compare', [ProviderCompareController::class, 'compare']);
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

        // In-App Chat API [C7]
        Route::get('/conversations', [ChatController::class, 'conversations']);
        Route::post('/conversations', [ChatController::class, 'startConversation']);
        Route::get('/conversations/{id}/messages', [ChatController::class, 'messages']);
        Route::post('/conversations/{id}/messages', [ChatController::class, 'sendMessage']);

        // Client Favorites & Job Requests / Proposals API
        Route::prefix('client')->group(function () {
            // Favorites
            Route::get('/favorites', [ClientFavoriteController::class, 'index']);
            Route::post('/favorites/{providerId}', [ClientFavoriteController::class, 'store']);
            Route::delete('/favorites/{providerId}', [ClientFavoriteController::class, 'destroy']);
            Route::get('/favorites/{providerId}/status', [ClientFavoriteController::class, 'status']);

            // Job Requests & Proposals
            Route::get('/job-requests', [JobRequestController::class, 'index']);
            Route::post('/job-requests', [JobRequestController::class, 'store']);
            Route::get('/job-requests/{id}', [JobRequestController::class, 'show']);
            Route::get('/job-requests/{id}/compare', [JobRequestController::class, 'compare']);
            Route::post('/proposals/{id}/accept', [ProposalController::class, 'accept']);
            Route::post('/proposals/{id}/reject', [ProposalController::class, 'reject']);
        });

        // Provider Dashboard API (Spotlight inspired with real DB data)
        Route::prefix('provider')->group(function () {
            Route::get('/dashboard', [ProviderDashboardController::class, 'data']);
            Route::post('/bookings/{id}/status', [ProviderDashboardController::class, 'updateBookingStatus']);
            Route::post('/availability', [ProviderDashboardController::class, 'toggleAvailability']);
            Route::post('/services', [ProviderDashboardController::class, 'createService']);
            Route::get('/services/{id}', [ProviderDashboardController::class, 'getService']);
            Route::put('/services/{id}', [ProviderDashboardController::class, 'updateService']);
            Route::delete('/services/{id}', [ProviderDashboardController::class, 'deleteService']);
            Route::post('/profile', [ProviderDashboardController::class, 'updateProfile']);
            Route::post('/portfolio', [ProviderDashboardController::class, 'uploadPortfolioPhoto']);
            Route::delete('/portfolio/{id}', [ProviderDashboardController::class, 'deletePortfolioPhoto']);

            // Proposals & Job Requests Feed
            Route::get('/job-requests', [JobRequestController::class, 'providerFeed']);
            Route::post('/job-requests/{id}/proposals', [ProposalController::class, 'store']);
            Route::get('/proposals', [ProposalController::class, 'providerIndex']);

            // Semantic booking action endpoints (with transition guards)
            Route::post('/bookings/{id}/accept',   [ProviderDashboardController::class, 'acceptBooking']);
            Route::post('/bookings/{id}/decline',  [ProviderDashboardController::class, 'declineBooking']);
            Route::post('/bookings/{id}/start',    [ProviderDashboardController::class, 'startBooking']);
            Route::post('/bookings/{id}/complete', [ProviderDashboardController::class, 'completeBooking']);
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
