<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\AdopterDashboardController;
use App\Http\Controllers\ShelterDashboardController;
use App\Http\Controllers\RescuerDashboardController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Auth\AdoptionApplicationController;
use App\Models\AdoptionApplication;
use App\Http\Controllers\AdopterPetListingsController;
use App\Http\Controllers\ShelterApplicationController;
use App\Http\Controllers\AdopterApplicationController as AdopterApplicationControllerAlias;
use App\Http\Controllers\ShelterVerificationController;

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

// Protected Routes with Authentication and Role-Based Middleware
Route::middleware(['auth'])->group(function () {

    // Admin Routes
    Route::middleware(['admin'])->group(function () {
        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
            ->name('admin.dashboard');
    });

    // Shelter Routes
    Route::middleware(['auth', 'shelter'])->group(function () {
        Route::get('/shelter/pet_applications', [\App\Http\Controllers\Auth\AdoptionApplicationController::class, 'index'])
            ->name('shelter.pet_applications'); // Add route name for blade usage        // Add routes for review, approve, reject, message, etc.
        
        // Shelter Verification Routes
        Route::get('/shelter/verification', [ShelterVerificationController::class, 'showVerificationForm'])
            ->name('shelter.verification.form');
        Route::post('/shelter/verification', [ShelterVerificationController::class, 'submitVerification'])
            ->name('shelter.verification.submit');

        Route::get('/shelter/dashboard', [ShelterController::class, 'dashboard'])->name('shelter.dashboard');
        Route::get('/shelter/profile', [ShelterController::class, 'profile'])->name('shelter.profile');
        Route::put('/shelter/profile', [ShelterController::class, 'updateProfile'])->name('shelter.profile.update');
        Route::put('/shelter/password', [ShelterController::class, 'updatePassword'])->name('shelter.password.update');
        Route::put('/shelter/notifications', [ShelterController::class, 'updateNotifications'])->name('shelter.notifications.update');
    });
    Route::middleware(['shelter'])->group(function () {
        Route::get('/shelter/dashboard', [ShelterDashboardController::class, 'index'])
            ->name('shelter.dashboard');

        Route::post('/shelter/pets', [ShelterDashboardController::class, 'store'])
            ->name('shelter.pets.store');

        Route::get('/shelter/pets', [ShelterDashboardController::class, 'pets'])
            ->name('shelter.pets');

        Route::get('/shelter/messages', function () {
            return view('shelter.messages');
        })->name('shelter.messages');

        
        Route::get('/shelter/profile', [ShelterDashboardController::class, 'profile'])->name('shelter.profile');
        Route::post('/shelter/profile/update', [ShelterDashboardController::class, 'updateProfile'])->name('shelter.profile.update');
        Route::post('/shelter/profile/password', [ShelterDashboardController::class, 'updatePassword'])->name('shelter.profile.password');
        Route::post('/shelter/profile/delete', [ShelterDashboardController::class, 'deleteAccount'])->name('shelter.profile.delete');


        Route::get('/shelter/applications', [\App\Http\Controllers\ShelterApplicationController::class, 'index'])->name('shelter.applications.index');
        Route::get('/shelter/applications/{id}', [\App\Http\Controllers\ShelterApplicationController::class, 'show'])->name('shelter.applications.show');
        Route::post('/shelter/applications/{id}/approve', [\App\Http\Controllers\ShelterApplicationController::class, 'approve'])->name('shelter.applications.approve');
        Route::post('/shelter/applications/{id}/reject', [\App\Http\Controllers\ShelterApplicationController::class, 'reject'])->name('shelter.applications.reject');
        Route::post('/shelter/applications/{id}/request-info', [\App\Http\Controllers\ShelterApplicationController::class, 'requestInfo'])->name('shelter.applications.requestInfo');

        // edit pet details
        Route::match(['put', 'patch'], '/shelter/pets/{pet}', [ShelterDashboardController::class, 'update'])->name('shelter.pets.update');
        // view pet details
        Route::get('/shelter/pets/{pet}/applications', [AdoptionApplicationController::class, 'forPet'])->name('applications.forPet');
        // delete a pet
        Route::delete('/shelter/pets/{pet}', [ShelterDashboardController::class, 'destroy'])->name('shelter.pets.destroy');
    });

    // Rescuer Routes
    Route::middleware(['rescuer'])->group(function () {
        Route::get('/rescuer/dashboard', [RescuerDashboardController::class, 'index'])
            ->name('rescuer.dashboard');
        Route::get('/rescuer/profile', [RescuerDashboardController::class, 'profile'])->name('rescuer.profile');
        Route::post('/rescuer/profile/update', [RescuerDashboardController::class, 'updateProfile'])->name('rescuer.profile.update');
        Route::post('/rescuer/profile/password', [RescuerDashboardController::class, 'updatePassword'])->name('rescuer.profile.password');
        Route::post('/rescuer/profile/delete', [RescuerDashboardController::class, 'deleteAccount'])->name('rescuer.profile.delete');
    });
    // Adopter Routes
    Route::middleware(['adopter'])->group(function () {
        Route::get('/adopter/dashboard', [AdopterDashboardController::class, 'index'])
            ->name('adopter.dashboard');
        Route::get('/adopter/profile', [\App\Http\Controllers\AdopterDashboardController::class, 'profile'])->name('adopter.profile');
        Route::post('/adopter/profile/update', [\App\Http\Controllers\AdopterDashboardController::class, 'updateProfile'])->name('adopter.profile.update');
        Route::post('/adopter/profile/password', [\App\Http\Controllers\AdopterDashboardController::class, 'updatePassword'])->name('adopter.profile.password');
        Route::post('/adopter/profile/notifications', [\App\Http\Controllers\AdopterDashboardController::class, 'updateNotifications'])->name('adopter.profile.notifications');
        Route::post('/adopter/profile/delete', [\App\Http\Controllers\AdopterDashboardController::class, 'deleteAccount'])->name('adopter.profile.delete');
        Route::get('/adopter/application-status', [\App\Http\Controllers\AdopterApplicationController::class, 'index'])->name('adopter.application-status');
        Route::post('/adopter/applications', [\App\Http\Controllers\AdopterApplicationController::class, 'store'])->name('adopter.applications.store');
    });
    Route::get('/adopter/dashboard', [AdopterDashboardController::class, 'index'])
        ->name('adopter.dashboard');
    Route::get('/adopter/pet-swipe', function () {
        return view('adopter.pet-swipe');
    })->name('adopter.pet-swipe');
    Route::get('/adopter/pet-listings', [\App\Http\Controllers\AdopterPetListingsController::class, 'index'])->name('adopter.pet-listings');
    Route::get('/adopter/pet-details', function () {
        return view('adopter.pet-details');
    })->name('adopter.pet-details');
    Route::get('/adopter/pet-personality-quiz', function () {
        return view('adopter.pet-personality-quiz');
    })->name('adopter.pet-personality-quiz');
    Route::get('/adopter/adoption-form', function () {
        return view('adopter.adoption-form');
    })->name('adopter.adoption-form');
    Route::get('/adopter/messages', function () {
        return view('adopter.messages');
    })->name('adopter.messages');
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

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [AdminDashboardController::class, 'users'])->name('admin.users');
    Route::get('/verifications', [AdminDashboardController::class, 'verifications'])->name('admin.verifications');
    Route::get('/verifications/{id}', [AdminDashboardController::class, 'showVerification'])->name('admin.verifications.show');
    Route::post('/verifications/{id}/approve', [AdminDashboardController::class, 'approveVerification'])->name('admin.verifications.approve');
    Route::post('/verifications/{id}/reject', [AdminDashboardController::class, 'rejectVerification'])->name('admin.verifications.reject');
    Route::get('/stray-reports', [AdminDashboardController::class, 'strayReports'])->name('admin.stray-reports');
    Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('admin.settings');
    
    // User Management
    Route::post('/users', [AdminDashboardController::class, 'storeUser'])->name('admin.users.store');
    Route::put('/users/{user}', [AdminDashboardController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{user}', [AdminDashboardController::class, 'deleteUser'])->name('admin.users.delete');
    Route::post('/users/{user}/toggle-status', [AdminDashboardController::class, 'toggleUserStatus'])->name('admin.users.toggle-status');
    Route::post('/users/bulk-action', [AdminDashboardController::class, 'bulkAction'])->name('admin.users.bulk-action');
    Route::get('/users/export', [AdminDashboardController::class, 'exportUsers'])->name('admin.users.export');

    // Stray Reports Management
    Route::post('/stray-reports/{report}/assign', [AdminDashboardController::class, 'assignReport'])->name('admin.stray-reports.assign');
    Route::post('/stray-reports/{report}/status', [AdminDashboardController::class, 'updateReportStatus'])->name('admin.stray-reports.status');

    // Settings Management
    Route::post('/settings', [AdminDashboardController::class, 'updateSettings'])->name('admin.settings.update');
});

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

Route::get('/dashboard', function () {
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
})->name('dashboard')->middleware('auth');


// S3 Upload
Route::post('/upload', function (Request $request) {
    if ($request->hasFile('photo')) {
        $fileName = $request->file('photo')->getClientOriginalName();
        $request->file('photo')->store('pawmatch-system', [
            'disk' => 's3',
            'visibility' => 'public'
        ]);
        return 'Uploaded!';
    }
    return 'No file uploaded.';
});


Route::post('/adopter/profile/update', [AdopterDashboardController::class, 'updateProfile'])->name('adopter.profile.update');

// API route for fetching pet details by ID
Route::get('/api/pets/{id}', function ($id) {
    $pet = \App\Models\Pet::with('shelter')->find($id);
    if (!$pet) {
        return response()->json(['error' => 'Pet not found'], 404);
    }
    // Compose the response to match frontend expectations
    return response()->json([
        'pet_id' => $pet->pet_id,
        'name' => $pet->name,
        'breed' => $pet->breed,
        'age' => $pet->age,
        'gender' => $pet->gender,
        'size' => $pet->size,
        'weight' => $pet->weight ?? 0,
        'status' => $pet->adoption_status ?? $pet->status ?? 'available',
        'description' => $pet->description,
        'images' => [$pet->image_url ?? 'https://placehold.co/400x300'], // Placeholder for now
        'is_favorite' => false, // Placeholder, implement favorite logic if needed
        'shelter' => [
            'name' => $pet->shelter->shelter_name ?? 'Unknown Shelter',
            'address' => $pet->shelter->location ?? 'Unknown Address',
            'phone' => $pet->shelter->contact_info ?? 'Unknown Phone',
        ],
    ]);
});
