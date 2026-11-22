<?php

use App\Http\Controllers\ProviderCompareWebController;
use App\Http\Controllers\PublicProviderWebController;
use App\Http\Controllers\PublicServiceWebController;
use App\Http\Controllers\Api\StripePaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Guest & client service search & discovery
Route::get('/services', [PublicServiceWebController::class, 'index'])->name('services.index');
Route::get('/services/{id}', [PublicServiceWebController::class, 'show'])->name('services.show');

// Provider side-by-side comparison (Rates, Experience, Reviews)
Route::get('/compare', [ProviderCompareWebController::class, 'index'])->name('providers.compare');
Route::get('/providers/compare', function () {
    return redirect('/compare' . (request()->getQueryString() ? '?' . request()->getQueryString() : ''));
});

// Guest & client public provider discovery & profile
Route::get('/providers', [PublicProviderWebController::class, 'index'])->name('providers.index');
Route::get('/providers/{id}', [PublicProviderWebController::class, 'show'])->name('providers.show');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::get('/reset-password', function () {
    return view('auth.reset-password');
})->name('password.reset');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/profile', function () {
    return view('profile');
})->name('profile');

Route::get('/messages', function () {
    return view('chat');
})->name('chat');

Route::get('/report', function () {
    return view('reports.create');
})->name('reports.create');

// Provider dedicated workspace pages
Route::get('/provider/bookings', function () {
    return view('provider.bookings');
})->name('provider.bookings');

Route::get('/dashboard/bookings', function () {
    return redirect('/provider/bookings');
});

Route::get('/provider/services', function () {
    return view('provider.services');
})->name('provider.services');

Route::get('/dashboard/services', function () {
    return redirect('/provider/services');
});

Route::get('/admin', function () {
    return redirect('/admin/dashboard');
})->name('admin');

Route::get('/admin/login', function () {
    return view('auth.login');
})->name('admin.login');

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/admin/users', function () {
    return view('admin.users');
})->name('admin.users');

Route::get('/admin/bookings', function () {
    return view('admin.bookings');
})->name('admin.bookings');

Route::get('/admin/services', function () {
    return view('admin.services');
})->name('admin.services');

Route::get('/admin/reports', function () {
    return view('admin.reports');
})->name('admin.reports');

Route::get('/admin/catalog', function () {
    return redirect('/admin/services');
})->name('admin.catalog');

// Stripe Payments & Webhook
Route::post('/stripe/webhook', [StripePaymentController::class, 'handleWebhook'])->name('stripe.webhook');

Route::get('/payment/success', function () {
    return view('payment.success');
})->name('payment.success');

Route::get('/payment/cancel', function () {
    return view('payment.cancel');
})->name('payment.cancel');

