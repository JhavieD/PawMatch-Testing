<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdoptionApplication;

class AdoptionApplicationController extends Controller
{
    // View all applications for a specific pet
    public function forPet(Request $request, $petId)
    {
        $applications = AdoptionApplication::where('pet_id', $petId)->latest()->get();
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['applications' => $applications]);
        }
        return view('shelter.pet_applications', compact('applications'));
    }
}
