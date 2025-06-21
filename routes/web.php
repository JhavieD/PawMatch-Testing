<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\AdopterDashboardController;
use App\Http\Controllers\ShelterController;
use App\Http\Controllers\RescuerController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Auth\AdoptionApplicationController;
use App\Models\AdoptionApplication;
use App\Http\Controllers\AdopterPetListingsController;
use App\Http\Controllers\ShelterApplicationController;
use App\Http\Controllers\AdopterApplicationController as AdopterApplicationControllerAlias;
use App\Http\Controllers\MessageController;

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
        Route::get('/shelter/pet_applications', [AdoptionApplicationController::class, 'index'])
            ->name('shelter.pet_applications');


        // Shelter dashboard and pets
        Route::get('/shelter/dashboard', [ShelterController::class, 'index'])->name('shelter.dashboard');
        Route::post('/shelter/pets', [ShelterController::class, 'store'])->name('shelter.pets.store');
        Route::get('/shelter/pets', [ShelterController::class, 'pets'])->name('shelter.pets');
        // Route::match(['put', 'patch'], '/shelter/pets/{pet}', [ShelterController::class, 'update'])->name('shelter.pets.update');
        // Route::delete('/shelter/pets/{pet}', [ShelterController::class, 'destroy'])->name('shelter.pets.destroy');
        // Route::get('/shelter/pets/{pet}/applications', [AdoptionApplicationController::class, 'forPet'])->name('applications.forPet');

        // Messages
        Route::get('/shelter/messages', [MessageController::class, 'shelterMessages'])->name('shelter.messages');

        // Profile
        Route::get('/shelter/profile', fn() => view('shelter.profile'))->name('shelter.profile');

        // Custom Application Controller
        Route::get('/shelter/applications', [ShelterApplicationController::class, 'index'])->name('shelter.applications.index');
        Route::get('/shelter/applications/{id}', [ShelterApplicationController::class, 'show'])->name('shelter.applications.show');
        Route::post('/shelter/applications/{id}/approve', [ShelterApplicationController::class, 'approve'])->name('shelter.applications.approve');
        Route::post('/shelter/applications/{id}/reject', [ShelterApplicationController::class, 'reject'])->name('shelter.applications.reject');
        Route::post('/shelter/applications/{id}/request-info', [ShelterApplicationController::class, 'requestInfo'])->name('shelter.applications.requestInfo');

        // edit pet details
        Route::match(['put', 'patch'], '/shelter/pets/{pet}', [ShelterController::class, 'update'])->name('shelter.pets.update');
        // view pet details
        Route::get('/shelter/pets/{pet}/applications', [AdoptionApplicationController::class, 'forPet'])->name('applications.forPet');
        // delete a pet
        Route::delete('/shelter/pets/{pet}', [ShelterController::class, 'destroy'])->name('shelter.pets.destroy');
    });

    // Messages Routes

    Route::middleware('auth')->group(function () {
        Route::get('/messages', [MessageController::class, 'index'])->name('messages.fetch');
        Route::post('/messages', [MessageController::class, 'send'])->name('messages.send');
    });

    // Rescuer Routes
    Route::middleware(['rescuer'])->group(function () {
        Route::get('/rescuer/dashboard', [RescuerController::class, 'index'])
            ->name('rescuer.dashboard');
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
    // changed message route
    Route::get(
        '/adopter/messages',
        [
            AdopterDashboardController::class,
            'messages'
        ]
    )->name('adopter.messages');
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
// Route::get('/messages', function () {
//     return 'Messages page coming soon!';
// })->name('messages.index');

// Placeholder route for profile.edit
Route::get('/profile/edit', function () {
    return 'Profile edit page coming soon!';
})->name('profile.edit');

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.admin_dashboard');
    })->name('admin.dashboard');
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
