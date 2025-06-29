<?php
namespace App\Http\Controllers\Shared;

use App\Models\StrayReports;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportStrayController extends Controller
{
    // Show the report stray form
    public function showForm()
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
            'state' => 'nullable|string',
            'zip' => 'nullable|string',
            // add other fields as needed
        ]);

        $adopter = Auth::user()->adopter; // assumes User has adopter() relationship

        $location = trim("{$request->street}, {$request->city}, {$request->state} {$request->zip}", ', ');

        StrayReports::create([
            'adopter_id' => $adopter ? $adopter->adopter_id : null,
            'location' => $location,
            'description' => $request->description,
            'status' => 'pending',
            'reported_at' => now(),
        ]);

        return redirect()->route('adopter.report-stray')->with('success', 'Stray report submitted!');
    }
}