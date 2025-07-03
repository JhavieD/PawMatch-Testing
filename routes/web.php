<?php

// =====================
// USE STATEMENTS
// =====================
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
// Auth Controllers
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\GoogleController;
// Adopter Controllers
use App\Http\Controllers\Adopter\AdopterDashboardController;
use App\Http\Controllers\Adopter\AdopterPetListingsController;
use App\Http\Controllers\Adopter\AdopterApplicationController;
use App\Http\Controllers\Adopter\AdopterReportController;
// Shelter Controllers
use App\Http\Controllers\Shelter\ShelterDashboardController;
use App\Http\Controllers\Shelter\ShelterVerificationController;
use App\Http\Controllers\Shelter\ShelterApplicationController;
use App\Http\Controllers\Shelter\AdoptionApplicationController as ShelterAdoptionApplicationController;
// Rescuer Controllers
use App\Http\Controllers\Rescuer\RescuerDashboardController;
use App\Http\Controllers\Rescuer\RescuerController;
use App\Http\Controllers\Rescuer\RescuerVerificationController;
// Admin Controllers
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\SettingsController;
// Shared Controllers
use App\Http\Controllers\Shared\MessageController;
use App\Http\Controllers\Shared\PetPersonalityQuizController;
use App\Http\Controllers\Shared\PetSwipeController;
use App\Http\Controllers\Shared\ReportStrayController;
use App\Http\Controllers\Rescuer\RescuerApplicationController;
use App\Models\Rescuer\Rescuer;
// Models
use App\Models\Shared\AdoptionApplication;

// =====================
// PUBLIC ROUTES
// =====================
Route::get('/', fn() => view('home'))->name('home');
Route::get('/faq', fn() => view('faq'))->name('faq');
Route::get('/terms', fn() => view('terms'))->name('terms');
Route::get('/about', fn() => view('about'))->name('about');
Route::get('/pet-listings', [App\Http\Controllers\Adopter\AdopterPetListingsController::class, 'index'])->name('pet-listings');
Route::get('/report-stray', fn() => view('report-stray'))->name('report-stray');
Route::get('/public-pet-listings', [App\Http\Controllers\Adopter\AdopterPetListingsController::class, 'publicIndex'])->name('public.pet-listings');
Route::get('/public-pet-details/{pet}', [App\Http\Controllers\Adopter\AdopterPetListingsController::class, 'publicPetDetails'])->name('public.pet-details');

// Pet Personality Quiz
Route::get('/quiz', [PetPersonalityQuizController::class, 'showQuiz'])->name('quiz.show');
Route::post('/quiz', [PetPersonalityQuizController::class, 'submitQuiz'])->name('quiz.submit');

// =====================
// AUTHENTICATION ROUTES
// =====================
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// =====================
// AUTHENTICATED USER ROUTES
// =====================
Route::middleware(['auth', 'check.user.status'])->group(function () {
    // -------- ADMIN --------
    Route::middleware(['admin'])->group(function () {
        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/users', [AdminDashboardController::class, 'users'])->name('admin.users');
        Route::get('/admin/verifications', [AdminDashboardController::class, 'verifications'])->name('admin.verifications');
        Route::get('/admin/verifications/{id}', [AdminDashboardController::class, 'showVerification'])->name('admin.verifications.show');
        Route::post('/admin/verifications/{id}/approve', [AdminDashboardController::class, 'approveVerification'])->name('admin.verifications.approve');
        Route::post('/admin/verifications/{id}/reject', [AdminDashboardController::class, 'rejectVerification'])->name('admin.verifications.reject');
        Route::get('/admin/stray-reports', [AdminDashboardController::class, 'strayReports'])->name('admin.stray-reports');
        Route::get('/admin/stray-reports/debug', [AdminDashboardController::class, 'debugStrayReports']); // Temporary debug route
        //added by A
        Route::post('/admin/stray-reports/{id}/comment', [AdminDashboardController::class, 'addComment']);
        Route::post('/admin/stray-reports/{id}/status', [AdminDashboardController::class, 'updateStatus']);
        Route::get('/admin/stray-reports/{id}/timeline', [AdminDashboardController::class, 'strayReportTimeline']);
        Route::get('/admin/stray-reports/{id}/comments', [AdminDashboardController::class, 'strayReportComments']);
        Route::post('/admin/stray-reports/{reportId}/mark-investigating', [AdminDashboardController::class, 'markAsInvestigating']);
        Route::get('/admin/stray-reports/{reportId}/nearby-shelters', [AdminDashboardController::class, 'findNearbyShelters'])->name('admin.stray-reports.nearby-shelters');

        // User Management
        Route::get('/admin/users/{user}', [AdminDashboardController::class, 'showUser'])->name('admin.users.show');
        Route::post('/admin/users', [AdminDashboardController::class, 'storeUser'])->name('admin.users.store');
        Route::put('/admin/users/{user}', [AdminDashboardController::class, 'updateUser'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [AdminDashboardController::class, 'deleteUser'])->name('admin.users.delete');
        Route::post('/admin/users/{user}/toggle-status', [AdminDashboardController::class, 'toggleUserStatus'])->name('admin.users.toggle-status');
        Route::post('/admin/users/bulk-action', [AdminDashboardController::class, 'bulkAction'])->name('admin.users.bulk-action');
        Route::get('/admin/users/export', [AdminDashboardController::class, 'exportUsers'])->name('admin.users.export');
        // Stray Reports Management
        Route::post('/admin/stray-reports/{report}/assign', [AdminDashboardController::class, 'assignReport'])->name('admin.stray-reports.assign');
        // Settings Management
        Route::post('/admin/settings/toggle-maintenance', [AdminDashboardController::class, 'toggleMaintenance'])->name('admin.toggle-maintenance');
        Route::get('/admin/settings', [AdminDashboardController::class, 'settings'])->name('admin.settings');
    });

    // -------- SHELTER --------
    Route::middleware(['shelter'])->group(function () {
        Route::get('/shelter/verification', [ShelterVerificationController::class, 'showVerificationForm'])->name('shelter.verification.form');
        Route::post('/shelter/verification', [ShelterVerificationController::class, 'submitVerification'])->name('shelter.verification.submit');
        Route::get('/shelter/dashboard', [ShelterDashboardController::class, 'index'])->name('shelter.dashboard');
        Route::post('/shelter/pets', [ShelterDashboardController::class, 'createPetListing'])->name('shelter.pets.create');
        Route::get('/shelter/pets', [ShelterDashboardController::class, 'pets'])->name('shelter.pets');
        Route::get('/shelter/messages', [MessageController::class, 'shelterMessages'])->name('shelter.messages');
        Route::get('/shelter/profile', [ShelterDashboardController::class, 'profile'])->name('shelter.profile');
        Route::post('/shelter/profile/update', [ShelterDashboardController::class, 'updateProfile'])->name('shelter.profile.update');
        Route::post('/shelter/profile/password', [ShelterDashboardController::class, 'updatePassword'])->name('shelter.profile.password');
        Route::post('/shelter/profile/delete', [ShelterDashboardController::class, 'deleteAccount'])->name('shelter.profile.delete');
        // PET APPLICATIONS CRUD ROUTES
        Route::get('/shelter/applications', [ShelterApplicationController::class, 'index'])->name('shelter.applications.index');
        Route::get('/shelter/applications/{id}/review', [ShelterApplicationController::class, 'reviewApplication'])->name('shelter.applications.review');
        Route::post('/shelter/applications/{id}/approve', [ShelterApplicationController::class, 'approve'])->name('shelter.applications.approve');
        Route::post('/shelter/applications/{id}/reject', [ShelterApplicationController::class, 'reject'])->name('shelter.applications.reject');
        Route::post('/shelter/applications/{id}/request-info', [ShelterApplicationController::class, 'requestInfo'])->name('shelter.applications.requestInfo');
        // PET MANAGEMENT CRUD ROUTES
        Route::match(['put', 'patch'], '/shelter/pets/{pet}', [ShelterDashboardController::class, 'update'])->name('shelter.pets.update');
        Route::get('/shelter/pets/{pet}/applications', [ShelterApplicationController::class, 'forPet'])->name('applications.forPet');
        Route::delete('/shelter/pets/{pet}', [ShelterDashboardController::class, 'destroy'])->name('shelter.pets.destroy');
        Route::delete('/shelter/pet-images/{id}', [ShelterDashboardController::class, 'deleteImage'])->name('shelter.pet-images.destroy');
        Route::get('/shelter/pet_applications', [ShelterAdoptionApplicationController::class, 'index'])->name('shelter.pet_applications');
        //STRAY REPORTS ROUTES ADDED BY ANDREa 12:22
        Route::get('/shelter/stray-reports', [ShelterDashboardController::class, 'strayReports'])->name('shelter.stray-reports');
        Route::post('/shelter/stray-reports/{reportId}/mark-read', [ShelterDashboardController::class, 'markStrayReportRead'])->name('shelter.stray-reports.mark-read');
        Route::post('/shelter/stray-reports/{reportId}/accept', [ShelterDashboardController::class, 'acceptStrayReport'])->name('shelter.stray-reports.accept');
    });

    // -------- MESSAGES (FOR ALL POT) --------
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.fetch');
    Route::post('/messages', [MessageController::class, 'sendMessage'])->name('messages.send');
    Route::post('/messages/mark-as-read', [MessageController::class, 'markAsRead'])->name('messages.markAsRead');

    // -------- RESCUER --------
    Route::middleware(['rescuer'])->group(function () {
        Route::get('/verification', [RescuerVerificationController::class, 'showVerificationForm'])->name('rescuer.verification.form');
        Route::post('/verification', [RescuerVerificationController::class, 'submitVerification'])->name('rescuer.verification.submit');
        Route::get('/rescuer/dashboard', [RescuerDashboardController::class, 'index'])->name('rescuer.dashboard');
        Route::get('/rescuer/profile', [RescuerDashboardController::class, 'profile'])->name('rescuer.profile');
        Route::get('/rescuer/pets', [RescuerDashboardController::class, 'petManagement'])->name('rescuer.pet-management');
        Route::get('/rescuer/pet_applications', [RescuerDashboardController::class, 'petApplications'])->name('rescuer.pet_applications');
        Route::get('/rescuer/rescuer-messages', [RescuerDashboardController::class, 'rescuerMessages'])->name('rescuer.messages');
        Route::get('/rescuer/messages', [MessageController::class, 'rescuerMessages'])->name('rescuer.messages');
        Route::post('/rescuer/pets', [RescuerDashboardController::class, 'AddPetListing'])->name('rescuer.pets.create');
        Route::post('/rescuer/profile/update', [RescuerDashboardController::class, 'updateProfile'])->name('rescuer.profile.update');
        Route::post('/rescuer/profile/password', [RescuerDashboardController::class, 'updatePassword'])->name('rescuer.profile.password');
        Route::post('/rescuer/prof  ile/delete', [RescuerDashboardController::class, 'deleteAccount'])->name('rescuer.profile.delete');

        // RESCUER PET APPLICATIONS CRUD ROUTES
        Route::get('/rescuer/pet_applications', [RescuerApplicationController::class, 'index'])->name('rescuer.pet_applications');
        Route::get('/rescuer/applications/{id}', [RescuerApplicationController::class, 'show'])->name('rescuer.pet_applications.show');
        Route::post('/rescuer/applications/{id}/approve', [RescuerApplicationController::class, 'approve'])->name('rescuer.pet_applications.approve');
        Route::post('/rescuer/applications/{id}/reject', [RescuerApplicationController::class, 'reject'])->name('rescuer.pet_applications.reject');
        Route::post('/rescuer/applications/{id}/request-info', [RescuerApplicationController::class, 'requestInfo'])->name('rescuer.pet_applications.requestInfo');

        // RESCUER PET MANAGEMENT CRUD ROUTES
        Route::get('/rescuer/pets/{pet}/applications', [RescuerApplicationController::class, 'forPet'])->name('rescuer.pets.applications');
        Route::post('/rescuer/pets', [RescuerDashboardController::class, 'AddPetListing'])->name('rescuer.pets.create');
        Route::match(['put', 'patch'], '/rescuer/pets/{pet}', [RescuerDashboardController::class, 'update'])->name('rescuer.pets.update');
        Route::delete('/rescuer/pets/{pet}', [RescuerDashboardController::class, 'destroy'])->name('rescuer.pets.destroy');
        Route::delete('/rescuer/pet-images/{id}', [RescuerDashboardController::class, 'deleteImage'])->name('rescuer.pet-images.destroy');
    });

    // -------- ADOPTER --------
    Route::middleware(['adopter'])->group(function () {
        Route::get('/adopter/dashboard', [AdopterDashboardController::class, 'index'])->name('adopter.dashboard');
        // Profile routes
        Route::get('/adopter/profile', [AdopterDashboardController::class, 'profile'])->name('adopter.profile');
        Route::post('/adopter/profile/update', [AdopterDashboardController::class, 'updateProfile'])->name('adopter.profile.update');
        Route::post('/adopter/profile/password', [AdopterDashboardController::class, 'updatePassword'])->name('adopter.profile.password');
        Route::post('/adopter/profile/notifications', [AdopterDashboardController::class, 'updateNotifications'])->name('adopter.profile.notifications');
        Route::post('/adopter/profile/delete', [AdopterDashboardController::class, 'deleteAccount'])->name('adopter.profile.delete');
        // Pet-related routes
        Route::get('/adopter/pets/{pet}', [AdopterPetListingsController::class, 'show'])->name('adopter.pets.show');
        Route::get('/adopter/pet-swipe', [PetSwipeController::class, 'index'])->name('adopter.pet-swipe');
        Route::get('/adopter/pet-listings', [AdopterPetListingsController::class, 'index'])->name('adopter.pet-listings');
        Route::post('/api/pets/{pet}/favorite', [AdopterPetListingsController::class, 'toggleFavorite']);
        Route::get('/adopter/pet-details', fn() => view('adopter.pet-details'))->name('adopter.pet-details');
        Route::get('/adopter/pet-personality-quiz', fn() => view('adopter.pet-personality-quiz'))->name('adopter.pet-personality-quiz');
        // Application routes
        Route::get('/adopter/adoption-form', fn() => view('adopter.adoption-form'))->name('adopter.adoption-form');
        Route::get('/adopter/application-status', [AdopterApplicationController::class, 'index'])->name('adopter.application-status');
        Route::post('/adopter/applications', [AdopterApplicationController::class, 'store'])->name('adopter.applications.store');
        // Messages route
        Route::get('/adopter/messages', [MessageController::class, 'adopterMessages'])->name('adopter.messages');
        // Stray reporting routes
        Route::get('/adopter/report-stray', [ReportStrayController::class, 'show'])->name('adopter.report-stray');
        Route::post('/stray-report/submit', [ReportStrayController::class, 'submit'])->name('stray.report.submit');
        // Report status routes
        Route::get('/my-reports', [App\Http\Controllers\Adopter\AdopterReportController::class, 'myReports'])->name('adopter.my-reports');
        Route::get('/reports/{reportId}', [App\Http\Controllers\Adopter\AdopterReportController::class, 'show'])->name('adopter.reports.show');
    });

    // -------- PROFILE & DASHBOARD REDIRECTS --------
    Route::view('profile', 'profile')->name('profile');
    Route::get('/dashboard-redirect', function () {
        $user = auth()->user();
        if (!$user) return redirect()->route('login');
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
    })->name('dashboard.redirect');
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if (!$user) return redirect()->route('login');
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
    })->name('dashboard');
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
    // API route for fetching pet details by ID
    Route::get('/api/pets/{id}', function ($id) {
        $pet = \App\Models\Shared\Pet::with('shelter')->find($id);
        if (!$pet) {
            return response()->json(['error' => 'Pet not found'], 404);
        }
        $isFavorite = false;
        $user = auth()->user();
        if ($user && $user->adopter) {
            $isFavorite = $user->adopter->savedPets()->where('saved_pets.pet_id', $pet->pet_id)->exists();
        }
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
            'images' => [$pet->image_url ?? 'https://placehold.co/400x300'],
            'is_favorite' => $isFavorite,
            'shelter_id' => $pet->shelter->shelter_id ?? null,
            'user_id' => $pet->shelter->user_id ?? null,
            'shelter' => [
                'name' => $pet->shelter->shelter_name ?? 'Unknown Shelter',
                'address' => $pet->shelter->location ?? 'Unknown Address',
                'phone' => $pet->shelter->contact_info ?? 'Unknown Phone',
            ],
        ]);
    });
    Route::get('/api/pets/{pet}/images', [ShelterDashboardController::class, 'getPetImages']);
});

// =====================
// PUBLIC API & PLACEHOLDER ROUTES
// =====================
Route::get('/application-status', fn() => view('adopter.application-status'))->name('application-status');
Route::get('/pets', fn() => 'Pet listings coming soon!')->name('pets.index');
Route::get('/applications', fn() => 'Applications page coming soon!')->name('applications.index');
Route::get('/profile/edit', fn() => 'Profile edit page coming soon!')->name('profile.edit');

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/users/{user}/activate', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'activateUser'])->name('users.activate');
    Route::post('/users/{user}/deactivate', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'deactivateUser'])->name('users.deactivate');
    Route::post('/users/{user}/ban', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'banUser'])->name('users.ban');
    Route::post('/users/{user}/unban', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'unbanUser'])->name('users.unban');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'deleteUser'])->name('users.delete');
});

//MGA MESSED UP NA NAGLOGIN AS USER DYAN
Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
});

Route::get('/debug-session', function () {
    return [
        'session_id' => session()->getId(),
        'user_id' => auth()->id(),
        'session' => session()->all(),
    ];
});

// Notification: Mark as read
Route::post('/notifications/{id}/read', function ($id) {
    $notification = \App\Models\Notification::where('id', $id)
        ->where('user_id', auth()->id())
        ->firstOrFail();
    $notification->is_read = true;
    $notification->read_at = now();
    $notification->save();
    return response()->json(['success' => true]);
})->middleware('auth');
