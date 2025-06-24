<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pet;

class PetSwipeController extends Controller
{
    public function index(Request $request)
    {
        $query = Pet::query();

        // Mapping quiz short values to actual DB values
        $speciesMap = [
            'dog' => 'Dog',
            'cat' => 'Cat',
            'either' => null, 
        ];

        $behaviorMap = [
            'calm' => 'Calm and Relaxed',
            'playful' => 'Playful and Energetic',
            'independent' => 'Independent',
            'protective' => 'Protective',
        ];

        $activityMap = [
            'low' => 'Low',
            'moderate' => 'Moderate',
            'high' => 'High',
        ];

        $eatingMap = [
            'balanced_diet' => 'Balanced Diet',
            'portion_control' => 'Portion Control',
            'consistent_schedule' => 'Consistent Feeding Schedule',
        ];

        // Filter by species (dog/cat)
        if (
            $request->filled('question1') &&
            isset($speciesMap[$request->input('question1')]) &&
            $speciesMap[$request->input('question1')] !== null
        ) {
            $query->where('species', $speciesMap[$request->input('question1')]);
        }

        // Filter by behavior/personality
        if ($request->filled('question2') && isset($behaviorMap[$request->input('question2')])) {
            $query->where('behavior', $behaviorMap[$request->input('question2')]);
        }

        // Filter by activity level
        if ($request->filled('question3') && isset($activityMap[$request->input('question3')])) {
            $query->where('daily_activity', $activityMap[$request->input('question3')]);
        }

        // Filter by special needs
        if ($request->filled('question4')) {
            if ($request->input('question4') === 'no') {
                $query->where(function($q) {
                    $q->whereNull('special_needs')
                      ->orWhere('special_needs', 'No')
                      ->orWhere('special_needs', '0');
                });
            } elseif ($request->input('question4') === 'yes') {
                $query->where('special_needs', 'Yes');
            }
        }

        // Filter by eating habits
        if ($request->filled('question5') && isset($eatingMap[$request->input('question5')])) {
            $query->where('eating_habits', $eatingMap[$request->input('question5')]);
        }

        $pets = $query->get();

        return view('adopter.pet-swipe', compact('pets'));
    }
}