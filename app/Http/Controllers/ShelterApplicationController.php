<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdoptionApplication;
use App\Models\ApplicationAnswer;
use Illuminate\Support\Facades\Auth;

class ShelterApplicationController extends Controller
{
    // List all applications for the shelter's pets
    public function index()
    {
        $shelter = Auth::user()->shelter;
        $applications = AdoptionApplication::where('shelter_id', $shelter->shelter_id)
            ->with(['adopter.user', 'pet', 'answers'])
            ->orderByDesc('submitted_at')
            ->get();
        return view('shelter.applications.index', compact('applications'));
    }

    // Show details of a specific application
    public function show($id)
    {
        $application = AdoptionApplication::with(['adopter.user', 'pet', 'answers'])
            ->findOrFail($id);
        if (request()->ajax()) {
            return response()->view('shelter.application_modal', compact('application'));
        }
        return view('shelter.applications.show', compact('application'));
    }

    // Approve an application
    public function approve($id)
    {
        $application = AdoptionApplication::findOrFail($id);
        $application->status = 'approved';
        $application->reviewed_at = now();
        $application->save();
        // Optionally notify adopter here
        return back()->with('success', 'Application approved!');
    }

    // Reject an application
    public function reject(Request $request, $id)
    {
        $application = AdoptionApplication::findOrFail($id);
        $application->status = 'rejected';
        $application->reviewed_at = now();
        $application->rejection_reason = $request->input('rejection_reason');
        $application->save();
        // Optionally notify adopter here
        return back()->with('success', 'Application rejected.');
    }

    // Request more info from applicant
    public function requestInfo(Request $request, $id)
    {
        // You can implement messaging or notification logic here
        // For now, just a placeholder
        return back()->with('info', 'Information request sent to applicant!');
    }
} 