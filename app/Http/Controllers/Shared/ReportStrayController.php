<?php

namespace App\Http\Controllers\Shared;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Shared\StrayReports;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportStrayController extends Controller
{
    public function show()
    {
        return view('adopter.report-stray');
    }

    // Handle the form submission
    public function submit(Request $request)
    {
        $request->validate([
            'animalType' => 'required|string',
            'description' => 'required|string',
            'street' => 'nullable|string',
            'city' => 'nullable|string',          
            'zip' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5024'
        ]);

        $adopter = Auth::user()->adopter;
        $location = trim("{$request->street}, {$request->city} {$request->zip}", ', ');

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('stray-reports', 's3');
            Storage::disk('s3')->setVisibility($path, 'public');
            $imageUrl = Storage::disk('s3')->url($path);
        }

        StrayReports::create([
            'adopter_id' => $adopter ? $adopter->adopter_id : null,
            'animal_type' => $request->animalType,
            'location' => $location,
            'description' => $request->description,
            'image_url' => $imageUrl,
            'status' => 'pending',
            'reported_at' => now(),
        ]);

        return redirect()->route('adopter.report-stray')->with('success', 'Stray report submitted successfully!');
    }
}