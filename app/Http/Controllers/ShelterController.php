<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShelterController extends Controller
{
    public function index()
    {
        $shelter = auth()->user()->shelter;

        // statistics
        $availablePets = $shelter->pets()->where('adoption_status', 'available')->count();
        $pendingApplications = $shelter->applications()->where('status', 'pending')->count();
        $successfulAdoptions = $shelter->applications()->where('status', 'approved')->count();
        $newMessages = $shelter->receivedMessages()->where('is_read', false)->count();
        $averageRating = round($shelter->adopterReviews()->avg('rating'), 1);
        $totalReviews = $shelter->adopterReviews()->count();


        // Recent items
        $recentPets = $shelter->pets()->latest()->take(2)->get();
        $recentApplications = $shelter->applications()->latest()->take(2)->get();
        $recentMessages = $shelter->receivedMessages()->latest()->take(2)->get();
        $recentReviews = $shelter->adopterReviews()->orderByDesc('created_at')->take(2)->get();

        return view('shelter.shelter_dashboard', compact(
            'availablePets',
            'pendingApplications',
            'successfulAdoptions',
            'newMessages',
            'averageRating',
            'totalReviews',
            'recentPets',
            'recentApplications',
            'recentMessages',
            'recentReviews'
        ));
    }

    public function applications()
    {
        return view('shelter.pet_applications');
    }
}
