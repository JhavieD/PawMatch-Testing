<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdoptionApplication;

class AdoptionApplicationController extends Controller
{
    // View all applications for a specific pet
    public function index(Request $request)
    {
        $shelterId = auth()->user()->shelter->shelter_id ?? auth()->user()->shelter->id ?? null;
        $applications = \App\Models\AdoptionApplication::with(['adopter.user', 'pet'])
            ->where('shelter_id', $shelterId)
            ->orderByDesc('submitted_at')
            ->get();
        return view('shelter.pet_applications', compact('applications'));
    }

    // Return applications for a specific pet as JSON
    public function forPet($petId)
    {
        $applications = \App\Models\AdoptionApplication::with(['adopter.user', 'pet'])
            ->where('pet_id', $petId)
            ->orderByDesc('submitted_at')
            ->get();

        // Format applications for frontend JS
        $formatted = $applications->map(function ($app) {
            return [
                'id' => $app->id,
                'applicant_name' => $app->adopter->user->name ?? 'Unknown',
                'submitted_at' => $app->submitted_at,
                'status' => ucfirst($app->status),
            ];
        });

        return response()->json(['applications' => $formatted]);
    }
}
