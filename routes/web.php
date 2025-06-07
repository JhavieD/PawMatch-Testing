<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\AdopterDashboardController;

// Public routes
Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/faq', function () {
    return view('faq');
})->name('faq');

Route::get('/terms', function () {
    return view('terms');
})->name('terms');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/pet-listings', function () {
    return view('adopter.pet-listings');
})->name('pet-listings');

Route::get('/report-stray', function () {
    return view('report-stray');
})->name('report-stray');

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

//Multiauth

// adopter routes
Route::middleware(['auth','adopterMiddleware'])->group(function(){

    Route::get('dashboard',[AdopterController::class,'index'])->name('dashboard');

});

// rescuer routes
Route::middleware(['auth','rescuerMiddleware'])->group(function(){

    Route::get('dashboard',[RescuerController::class,'index'])->name('dashboard');

});

// shelter routes
Route::middleware(['auth','shelterMiddleware'])->group(function(){

    Route::get('/shelter/dashboard',[ShelterController::class,'index'])->name('shelter.dashboard');

});

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

// Admin dashboard
Route::get('/admin/dashboard', function () {
    return view('dashboards.admin-dashboard');
})->name('admin.dashboard');


// Report stray
Route::get('/report-stray', function () {
    return view('stray.report-stray');
})->name('report-stray');

Route::get('/dashboard-redirect', function () {
    $user = auth()->user();
    if (!$user) {
        return redirect()->route('login');
    }
    switch ($user->role) {
        case 'admin':
            return redirect()->route('admin.dashboard');
        case 'adopter':
            return redirect()->route('adopter.dashboard');
        case 'shelter':
            return redirect()->route('shelter.dashboard');
        case 'rescuer':
            return redirect()->route('rescuer.dashboard');
        default:
            return redirect()->route('home');
    }
})->name('dashboard.redirect')->middleware('auth');

Route::get('/application-status', function () {
    return view('adopter.application-status');
})->name('application-status');
