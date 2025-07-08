<?php

namespace App\Http\Controllers\Adopter;

use Illuminate\Http\Request;
use App\Models\Shared\Pet;
use App\Http\Controllers\Controller;

class AdopterPetListingsController extends Controller
{
    public function index(Request $request)
    {
        // Get unique pet types, age groups, and sizes
        $petTypes = Pet::pluck('species')->unique()->values();
        $ageGroups = Pet::pluck('age')->unique()->values();
        $sizes = Pet::pluck('size')->unique()->values();
        
        // Get pets, optionally filter by type, age, size, or search
        $query = Pet::query();
        if ($request->has('type')) {
            $query->whereIn('species', $request->input('type'));
        }
        if ($request->has('age')) {
            $query->whereIn('age', $request->input('age'));
        }
        if ($request->has('size')) {
            $query->whereIn('size', $request->input('size'));
        }
        if ($request->has('match_purpose') && $request->input('match_purpose')) {
            $adopterPurpose = auth()->user()->adopter->purpose;
            if ($adopterPurpose) {
                $query->where('suitable_for', $adopterPurpose);
            }
        }
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('breed', 'like', "%$search%")
                    ->orWhere('location', 'like', "%$search%")
                    ->orWhereHas('shelter', function ($q2) use ($search) {
                        $q2->where('city', 'like', "%$search%")
                            ->orWhere('shelter_name', 'like', "%$search%")
                            ->orWhere('address', 'like', "%$search%")
                            ->orWhere('province', 'like', "%$search%")
                            ->orWhere('region', 'like', "%$search%")
                            ->orWhere('barangay', 'like', "%$search%")
                            ->orWhere('zip_code', 'like', "%$search%")
                            ->orWhere('phone', 'like', "%$search%")
                            ->orWhere('email', 'like', "%$search%")
                            ->orWhere('contact_person', 'like', "%$search%")
                            ->orWhere('website', 'like', "%$search%")
                            ->orWhere('facebook', 'like', "%$search%")
                            ->orWhere('instagram', 'like', "%$search%")
                            ->orWhere('twitter', 'like', "%$search%")
                            ->orWhere('tiktok', 'like', "%$search%")
                            ->orWhere('youtube', 'like', "%$search%")
                            ->orWhere('other_social', 'like', "%$search%")
                            ->orWhere('description', 'like', "%$search%")
                            ->orWhere('mission', 'like', "%$search%")
                            ->orWhere('vision', 'like', "%$search%")
                            ->orWhere('history', 'like', "%$search%")
                            ->orWhere('achievements', 'like', "%$search%")
                            ->orWhere('awards', 'like', "%$search%")
                            ->orWhere('certifications', 'like', "%$search%")
                            ->orWhere('affiliations', 'like', "%$search%")
                            ->orWhere('partnerships', 'like', "%$search%")
                            ->orWhere('volunteer_opportunities', 'like', "%$search%")
                            ->orWhere('donation_methods', 'like', "%$search%")
                            ->orWhere('events', 'like', "%$search%")
                            ->orWhere('news', 'like', "%$search%")
                            ->orWhere('testimonials', 'like', "%$search%")
                            ->orWhere('faq', 'like', "%$search%")
                            ->orWhere('policies', 'like', "%$search%")
                            ->orWhere('adoption_process', 'like', "%$search%")
                            ->orWhere('adoption_requirements', 'like', "%$search%")
                            ->orWhere('adoption_fees', 'like', "%$search%")
                            ->orWhere('adoption_hours', 'like', "%$search%")
                            ->orWhere('adoption_location', 'like', "%$search%")
                            ->orWhere('adoption_contact', 'like', "%$search%")
                            ->orWhere('adoption_email', 'like', "%$search%")
                            ->orWhere('adoption_phone', 'like', "%$search%")
                            ->orWhere('adoption_website', 'like', "%$search%")
                            ->orWhere('adoption_facebook', 'like', "%$search%")
                            ->orWhere('adoption_instagram', 'like', "%$search%")
                            ->orWhere('adoption_twitter', 'like', "%$search%")
                            ->orWhere('adoption_tiktok', 'like', "%$search%")
                            ->orWhere('adoption_youtube', 'like', "%$search%")
                            ->orWhere('adoption_other_social', 'like', "%$search%")
                            ->orWhere('adoption_description', 'like', "%$search%")
                            ->orWhere('adoption_mission', 'like', "%$search%")
                            ->orWhere('adoption_vision', 'like', "%$search%")
                            ->orWhere('adoption_history', 'like', "%$search%")
                            ->orWhere('adoption_achievements', 'like', "%$search%")
                            ->orWhere('adoption_awards', 'like', "%$search%")
                            ->orWhere('adoption_certifications', 'like', "%$search%")
                            ->orWhere('adoption_affiliations', 'like', "%$search%")
                            ->orWhere('adoption_partnerships', 'like', "%$search%")
                            ->orWhere('adoption_volunteer_opportunities', 'like', "%$search%")
                            ->orWhere('adoption_donation_methods', 'like', "%$search%")
                            ->orWhere('adoption_events', 'like', "%$search%")
                            ->orWhere('adoption_news', 'like', "%$search%")
                            ->orWhere('adoption_testimonials', 'like', "%$search%")
                            ->orWhere('adoption_faq', 'like', "%$search%")
                            ->orWhere('adoption_policies', 'like', "%$search%");
                            })
                            ->orWhereHas('rescuer', function ($q2) use ($search) {
                                $q2->where('city', 'like', "%$search%")
                                    ->orWhere('organization_name', 'like', "%$search%")
                                    ->orWhere('address', 'like', "%$search%")
                                    ->orWhere('location', 'like', "%$search%")
                                    ->orWhere('contact_info', 'like', "%$search%");
                    });
            });
        }
        $pets = $query->with(['shelter', 'rescuer', 'images'])->paginate(12);

        return view('adopter.pet-listings', compact('petTypes', 'ageGroups', 'sizes', 'pets'));
    }

    public function show(Pet $pet)
    {
        $pet->load(['images', 'shelter', 'rescuer']);
        return view('adopter.pet-details', compact('pet'));
    }

    /**
     * Toggle favorite (save/remove) for a pet for the logged-in adopter.
     */
    public function toggleFavorite(Request $request, $petId)
    {
        $user = auth()->user();
        if (!$user || !$user->adopter) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $adopter = $user->adopter;
        $pet = Pet::findOrFail($petId);
        $isFavorite = $adopter->savedPets()->where('saved_pets.pet_id', $petId)->exists();
        if ($isFavorite) {
            $adopter->savedPets()->detach($petId);
        } else {
            $adopter->savedPets()->attach($petId);
        }
        // Clear dashboard cache for this user
        $userId = $user->user_id;
        \Cache::forget("adopter_dashboard_{$userId}");
        \Cache::forget("user_profile_image_{$userId}");
        return response()->json(['is_favorite' => !$isFavorite]);
    }

    /**
     * Get pet details including favorite status for the current user.
     */
    public function getPetDetails(Pet $pet)
    {
        // Force a fresh load of relationships to avoid stale data
        $pet = Pet::with(['images', 'shelter', 'rescuer'])->find($pet->pet_id);
        $user = auth()->user();
        $isFavorite = false;
        
        if ($user && $user->adopter) {
            // Force a fresh check of favorite status
            $isFavorite = $user->adopter->savedPets()
                ->where('saved_pets.pet_id', $pet->pet_id)
                ->exists();
        }
        
        if ($pet->shelter) {
            $pet->user_id = $pet->shelter->user_id;
        } elseif ($pet->rescuer) {
            $pet->user_id = $pet->rescuer->user_id;
        }
        
        $petData = $pet->toArray();
        $petData['is_favorite'] = $isFavorite;
        
        return response()->json($petData);
    }

    /**
     * Get all images for a specific pet.
     */
    public function getPetImages(Pet $pet)
    {
        $pet->load('images');
        return response()->json([
            'images' => $pet->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image_url' => $image->image_url
                ];
            })
        ]);
    }

    public function publicIndex(Request $request)
    {
        $pets = \App\Models\Shared\Pet::where('adoption_status', 'available')
            ->with('shelter', 'images')
            ->paginate(12);
        return view('public.pet-listings', compact('pets'));
    }

    public function publicPetDetails(Pet $pet)
    {
        $pet->load(['images', 'shelter']);
        return view('public.pet-details', compact('pet'));
    }
}

