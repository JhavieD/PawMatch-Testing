<?php

namespace App\Http\Controllers\Rescuer;

use App\Models\Rescuer\RescuerVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Http\Controllers\Shared\Controller;

class RescuerVerificationController extends Controller
{
    public function showVerificationForm()
    {
        $verification = RescuerVerification::where('rescuer_id', Auth::user()->rescuer->rescuer_id)
            ->latest()
            ->first();

        return view('rescuer.verification', compact('verification'));
    }

    public function submitVerification(Request $request)
    {
        $request->validate([
            'registration_doc' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
            'facebook_link' => 'nullable|url'
        ]);

        $existingVerification = RescuerVerification::where('rescuer_id', Auth::user()->rescuer->rescuer_id)
            ->where('status', 'pending')
            ->first();

        if ($existingVerification) {
            return redirect()->back()->with('error', 'You already have a pending verification request.');
        }
        

        $path = $request->file('registration_doc')->store('rescuer-verifications', 's3', 'public');

        RescuerVerification::create([
            'rescuer_id' => Auth::user()->rescuer->rescuer_id,
            'submitted_by' => Auth::id(),
            'document_url' => $path, // ← Now fully functional
            'facebook_link' => $request->facebook_link,
            'status' => 'pending',
            'submitted_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', 'Your verification request has been submitted successfully.');
    }
    
    public function approveVerification($id, Request $request)
    {
        $verification = RescuerVerification::findOrFail($id);
        $verification->status = 'approved';
        $verification->notes = $request->input('notes');
        $verification->save();

        return redirect()->route('admin.verifications')->with('success', 'Verification approved successfully.');
    }
}