<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

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

Route::get('/admin/catalog', function () {
    return redirect('/admin/services');
})->name('admin.catalog');
