<?php

namespace App\Http\Controllers\Adopter;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shared\AdoptionApplication;
use App\Models\Adopter\AdopterReview;
use App\Http\Controllers\Controller;

class AdopterApplicationController extends Controller
{
    public function index()
    {
        $adopter = Auth::user()->adopter;
        if (!$adopter) {
            abort(403, 'Adopter profile not found.');
        }
        $applications = AdoptionApplication::where('adopter_id', $adopter->adopter_id)
            ->with(['pet.shelter'])
            ->orderByDesc('submitted_at')
            ->get();

         // Get existing reviews for completed applications
        $reviews = AdopterReview::where('adopter_id', $adopter->adopter_id)->get()->keyBy(function($review) {
            return $review->shelter_id ? 'shelter_' . $review->shelter_id : 'rescuer_' . $review->rescuer_id;
        });
        
        return view('adopter.application-status', compact('applications'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $user = Auth::user();
        $adopter = $user->adopter;
        if (!$adopter) {
            return response()->json(['error' => 'Adopter profile not found'], 422);
        }
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,pet_id',
            'reason_for_adoption' => 'required|string',
            'living_arrangement' => 'required|string',
            'experience_with_pets' => 'required|string',
            'household_members' => 'required',
            'allergies' => 'required',
            'has_other_pets' => 'required',
            'other_pets_details' => 'nullable|string',
            'can_provide_vet_care' => 'required',
        ]);
        $pet = \App\Models\Shared\Pet::find($validated['pet_id']);
        $application = AdoptionApplication::create([
            'adopter_id' => $adopter->adopter_id,
            'pet_id' => $pet->pet_id,
            'shelter_id' => $pet->shelter_id,
            'rescuer_id' => $pet->rescuer_id,
            'reason_for_adoption' => $validated['reason_for_adoption'],
            'living_environment' => $validated['living_arrangement'],
            'experience_with_pets' => $validated['experience_with_pets'],
            'household_members' => (string) $validated['household_members'],
            'allergies' => $validated['allergies'],
            'has_other_pets' => $validated['has_other_pets'],
            'other_pets_details' => $validated['other_pets_details'] ?? null,
            'can_provide_vet_care' => $validated['can_provide_vet_care'],
            'status' => 'pending',
            'submitted_at' => now(),
        ]);
        return response()->json(['success' => true, 'application_id' => $application->application_id]);
    }
    
    public function complete($id)
    {
        $adopter = Auth::user()->adopter;
        if (!$adopter) {
            return response()->json(['error' => 'Adopter profile not found'], 403);
        }

        $application = AdoptionApplication::where('application_id', $id)
            ->where('adopter_id', $adopter->adopter_id)
            ->first();

        if (!$application) {
            return response()->json(['error' => 'Application not found'], 404);
        }

        if ($application->status !== 'approved') {
            return response()->json(['error' => 'Application must be approved before completing'], 400);
        }

        $application->status = 'completed';
        $application->save();

        return response()->json(['success' => true, 'message' => 'Meet & greet completed successfully']);
    }

    public function submitReview(Request $request, $id)
    {
        $adopter = Auth::user()->adopter;
        if (!$adopter) {
            return response()->json(['error' => 'Adopter profile not found'], 403);
        }

        $application = AdoptionApplication::where('application_id', $id)
            ->where('adopter_id', $adopter->adopter_id)
            ->where('status', 'completed')
            ->first();

        if (!$application) {
            return response()->json(['error' => 'Completed application not found'], 404);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|max:1000',
        ]);

        // Check if a review already exists for this adopter and shelter/rescuer combination
        $existingReview = AdopterReview::where('adopter_id', $adopter->adopter_id)
            ->where(function($query) use ($application) {
                if ($application->shelter_id) {
                    $query->where('shelter_id', $application->shelter_id);
                } elseif ($application->rescuer_id) {
                    $query->where('rescuer_id', $application->rescuer_id);
                }
            })
            ->first();

        if ($existingReview) {
            // Update existing review
            $existingReview->update([
                'rating' => $validated['rating'],
                'review' => $validated['review'],
                'created_at' => now(),
            ]);
            $review = $existingReview;
        } else {
            // Create new review
            $reviewData = [
                'adopter_id' => $adopter->adopter_id,
                'rating' => $validated['rating'],
                'review' => $validated['review'],
                'created_at' => now(),
            ];

            // Set either shelter_id or rescuer_id based on the application
            if ($application->shelter_id) {
                $reviewData['shelter_id'] = $application->shelter_id;
            } elseif ($application->rescuer_id) {
                $reviewData['rescuer_id'] = $application->rescuer_id;
            }

            $review = AdopterReview::create($reviewData);
        }

        return response()->json([
            'success' => true, 
            'message' => 'Review submitted successfully',
            'review' => $review
        ]);
    }
}
