<?php

namespace App\Http\Controllers;

use App\Models\ShelterVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ShelterVerificationController extends Controller
{
    public function showVerificationForm()
    {
        $verification = ShelterVerification::where('shelter_id', Auth::user()->shelter->shelter_id)
            ->latest()
            ->first();

        return view('shelter.verification', compact('verification'));
    }

    public function submitVerification(Request $request)
    {
        $request->validate([
            'registration_doc' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
            'facebook_link' => 'nullable|url'
        ]);

        // Check if there's already a pending verification
        $existingVerification = ShelterVerification::where('shelter_id', Auth::user()->shelter->shelter_id)
            ->where('status', 'pending')
            ->first();

        if ($existingVerification) {
            return redirect()->back()->with('error', 'You already have a pending verification request.');
        }

        // Store the document
        $path = $request->file('registration_doc')->store('shelter-verifications', 'public');

        // Create verification record
        ShelterVerification::create([
            'shelter_id' => Auth::user()->shelter->shelter_id,
            'submitted_by' => Auth::id(),
            'registration_doc_url' => $path,
            'facebook_link' => $request->facebook_link,
            'status' => 'pending',
            'submitted_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', 'Your verification request has been submitted successfully.');
    }
} 