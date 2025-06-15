<?php

namespace App\Http\Controllers;

use App\Models\AdoptionApplication;
use Illuminate\Http\Request;
use App\Models\Pet;
use App\Models\Application;
use App\Models\Message;

class AdopterDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get favorite pets (you'll need to implement this relationship)
        $favoritePets = $user->favoritePets?->savedPets ?? collect();
        
        // Get recent applications via adopter relationship
        $adopter = $user->adopter()->first();
        $recentApplications = $adopter ? $adopter->applications()->with(['pet', 'shelter'])->latest()->take(5)->get() : collect();
        
        // Get recent messages (temporarily disabled)
        $recentMessages = collect();
        
        return view('adopter.adopter_dashboard', compact(
            'favoritePets',
            'recentApplications',
            'recentMessages'
        ));
    }
}