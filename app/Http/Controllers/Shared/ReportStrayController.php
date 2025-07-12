<?php

namespace App\Http\Controllers\Shared;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Shared\StrayReports;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Shared\StrayReportStatusLog;


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
            'description' => 'required|string',
            'street' => 'required|string',
            'city' => 'required|string',
            'zip' => 'required|string',
            'photos' => 'nullable|array',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5024'
        ]);

        $adopter = Auth::user()->adopter;
        $location = trim("{$request->street}, {$request->city} {$request->zip}", ', ');

        $imageUrls = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('strayreport', 's3');
                Storage::disk('s3')->setVisibility($path, 'public');
                $imageUrls[] = Storage::disk('s3')->url($path);
            }
        }

        $report = StrayReports::create([
            'adopter_id' => $adopter ? $adopter->adopter_id : null,
            'location' => $location,
            'description' => $request->description,
            'image_url' => !empty($imageUrls) ? json_encode($imageUrls) : null,
            'status' => 'pending',
            'reported_at' => now(),
        ]);

        // Create initial status log entry - Fix: use empty string instead of null for old_status
        StrayReportStatusLog::create([
            'adopter_id' => $report->report_id, // Store report_id in adopter_id field
            'old_status' => '', // Fix: use empty string instead of null
            'new_status' => 'pending',
            'changed_by' => auth()->id(),
            'changed_at' => now(),
            'notes' => 'Stray animal report submitted. Our team will review and investigate this report.'
        ]);

        return redirect()->route('adopter.report-stray')->with('success', 'Stray report submitted successfully!');
    }
}