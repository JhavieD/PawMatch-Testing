<?php

namespace App\Http\Controllers\Rescuer;

use App\Http\Controllers\Shared\Controller;
use Illuminate\Http\Request;
use App\Models\Shared\AdoptionApplication;


class RescuerApplicationController extends Controller
{
    public function index(Request $request)
    {
        $rescuer = auth()->user()->rescuer;
        $petIds = $rescuer->pets()->pluck('pet_id');
        $applications = AdoptionApplication::with(['adopter.user', 'pet'])
            ->whereIn('pet_id', $petIds)
            ->orderByDesc('submitted_at')
            ->get();
        return view('rescuer.pet_applications', compact('applications'));
    }

    public function reviewApplication($id)
    {
        $application = AdoptionApplication::with(['adopter.user', 'pet'])->findOrFail($id);
        if (request()->ajax()) {
            return response()->view('rescuer.rescuer-application_modal', compact('application'));
        }
        return view('rescuer.rescuer-application_modal', compact('application'));
    }

    public function approve($id)
    {
        $application = AdoptionApplication::findOrFail($id);
        $application->status = 'approved';
        $application->save();
        return response()->json(['success' => true]);
    }

    // Reject an application
    public function reject(Request $request, $id)
    {
        $application = AdoptionApplication::findOrFail($id);
        $request->validate(['rejection_reason' => 'required|string|max:255']);
        $application->status = 'rejected';
        $application->rejection_reason = $request->input('rejection_reason');
        $application->save();
        return response()->json(['success' => true]);
    }

    public function requestInfo($id)
    {
        $application = AdoptionApplication::findOrFail($id);
        $application->status = 'info-requested';
        $application->reviewed_at = now();
        $application->save();
        return response()->json(['message' => 'Information request sent successfully.']);
    }


    public function forPet($petId)
    {
        $applications = \App\Models\Shared\AdoptionApplication::where('pet_id', $petId)
            ->with(['adopter.user', 'pet', 'answers'])
            ->orderByDesc('submitted_at')
            ->get();
        return response()->json(['applications' => $applications]);
    }

    public function complete($id)
    {
        $application = AdoptionApplication::findOrFail($id);
        $application->status = 'completed';
        $application->save();
        return response()->json(['success' => true]);
    }

    public function cancel($id)
    {
        $application = AdoptionApplication::findOrFail($id);
        $application->status = 'cancelled';
        $application->save();
        return response()->json(['success' => true]);
    }
}
