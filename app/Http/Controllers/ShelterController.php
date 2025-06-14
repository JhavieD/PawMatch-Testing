<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

    public function pets()
    {
        $shelter = auth()->user()->shelter;
        $pets = $shelter->pets()->latest()->get();
        return view('shelter.pets', compact('pets'));
    }
    // for addingpets
    public function store(Request $request)
    {
        $shelter = auth()->user()->shelter;
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|string',
            'breed' => 'required|string',
            'age' => 'required|integer',
            'gender' => 'required|string',
            'size' => 'required|string',
            'description' => 'nullable|string',
            'behavior' => 'nullable|string',
            'daily_activity' => 'nullable|string',
            'special_needs' => 'nullable|string',
            'compatibility' => 'nullable|string',
        ]);
        $data['adoption_status'] = 'available'; // always set to available on add
        $data['shelter_id'] = $shelter->shelter_id;

        // Debug: log all request and validated data
        Log::info('Pet Add Request Data', $request->all());
        Log::info('Pet Add Validated Data', $data);

        $pet = \App\Models\Pet::create($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('shelter.pets')->with('success', 'Pet added successfully!');
    }
    // for editing pets
    public function update(Request $request, $petId)
    {
        $pet = \App\Models\Pet::findOrFail($petId);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|string',
            'breed' => 'required|string',
            'age' => 'required|integer',
            'gender' => 'required|string',
            'size' => 'required|string',
            'description' => 'nullable|string',
            'adoption_status' => 'required|string',
            'behavior' => 'nullable|string',
            'daily_activity' => 'nullable|string',
            'special_needs' => 'nullable|string',
            'compatibility' => 'nullable|string',
        ]);
        $pet->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('shelter.pets')->with('success', 'Pet updated successfully!');
    }

    // for deleting pets
    public function destroy($petId)
    {
        $pet = \App\Models\Pet::findOrFail($petId);
        $pet->delete();
        return redirect()->route('shelter.pets')->with('success', 'Pet deleted successfully!');
    }
}
