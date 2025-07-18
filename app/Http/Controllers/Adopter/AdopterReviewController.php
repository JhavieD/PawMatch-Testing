<?php

namespace App\Http\Controllers\Adopter;

use App\Http\Controllers\Controller;
use App\Models\Adopter\AdopterReview;
use App\Models\Shared\AdoptionApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdopterReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'application_id' => 'required|exists:adoption_applications,application_id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $adopter = Auth::user()->adopter;
        $application = AdoptionApplication::findOrFail($request->application_id);
        
        // Check if a review already exists for this adopter and shelter/rescuer combination
        $existingReview = AdopterReview::where('adopter_id', $adopter->adopter_id)
            ->where('application_id', $application->application_id) // <-- add this line
            ->where(function($query) use ($application) {
                if ($application->shelter_id) {
                    $query->where('shelter_id', $application->shelter_id);
                } elseif ($application->rescuer_id) {
                    $query->where('rescuer_id', $application->rescuer_id);
                }
            })
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this shelter/rescuer for this application.'
            ]);
        }

        AdopterReview::create([
            'adopter_id' => $adopter->adopter_id,
            'application_id' => $application->application_id,
            'shelter_id' => $application->shelter_id,
            'rescuer_id' => $application->rescuer_id,
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully!'
        ]);
    }

    public function checkExistingReview(Request $request)
    {
        $adopter = Auth::user()->adopter;
        $application = AdoptionApplication::findOrFail($request->application_id);
        
        $existingReview = AdopterReview::where('adopter_id', $adopter->adopter_id)
            ->where('application_id', $application->application_id) // <-- add this line
            ->where(function($query) use ($application) {
                if ($application->shelter_id) {
                    $query->where('shelter_id', $application->shelter_id);
                } elseif ($application->rescuer_id) {
                    $query->where('rescuer_id', $application->rescuer_id);
                }
            })
            ->first();

        return response()->json([
            'hasReview' => $existingReview ? true : false,
            'review' => $existingReview ? [
                'rating' => $existingReview->rating,
                'review' => $existingReview->review,
                'created_at' => $existingReview->created_at->format('M d, Y')
            ] : null
        ]);
    }
}