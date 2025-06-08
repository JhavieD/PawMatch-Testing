<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\AdopterDashboardController;

// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    // Password Reset Routes
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
        ->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Adopter Routes
    Route::get('/adopter/dashboard', [AdopterDashboardController::class, 'index'])
        ->name('adopter.dashboard');

    // Shelter Routes
    Route::get('/shelter/dashboard', function () {
        return view('dashboards.shelter_dashboard');
    })->name('shelter.dashboard');

    // Rescuer Routes
    Route::get('/rescuer/dashboard', function () {
        return view('dashboards.rescuer_dashboard');
    })->name('rescuer.dashboard');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Placeholder route for pets.index
Route::get('/pets', function () {
    return 'Pet listings coming soon!';
})->name('pets.index');

// Placeholder route for applications.index
Route::get('/applications', function () {
    return 'Applications page coming soon!';
})->name('applications.index');

// Placeholder route for messages.index
Route::get('/messages', function () {
    return 'Messages page coming soon!';
})->name('messages.index');

// Placeholder route for profile.edit
Route::get('/profile/edit', function () {
    return 'Profile edit page coming soon!';
})->name('profile.edit');

// Placeholder route for admin.dashboard
Route::get('/admin/dashboard', function () {
    return 'Admin dashboard coming soon!';
})->name('admin.dashboard');

