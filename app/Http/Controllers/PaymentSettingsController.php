<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shelter\Shelter;
use App\Models\Rescuer\Rescuer;

class PaymentSettingsController extends Controller
{
    /**
     * Update shelter payment settings
     */
    public function updateShelterPayment(Request $request)
    {
        $request->validate([
            'adoption_fee' => 'required|numeric|min:0',
            'bank_name' => 'required|string|max:255',
            'bank_account_name' => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:255',
        ]);

        $shelter = Auth::user()->shelter;
        
        if (!$shelter) {
            return redirect()->back()->with('error', 'Shelter not found.');
        }

        $shelter->update([
            'adoption_fee' => $request->adoption_fee,
            'bank_name' => $request->bank_name,
            'bank_account_name' => $request->bank_account_name,
            'bank_account_number' => $request->bank_account_number,
        ]);

        return redirect()->back()->with('success', 'Payment settings updated successfully.');
    }

    /**
     * Update rescuer payment settings
     */
    public function updateRescuerPayment(Request $request)
    {
        $request->validate([
            'adoption_fee' => 'required|numeric|min:0',
            'bank_name' => 'required|string|max:255',
            'bank_account_name' => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:255',
        ]);

        $rescuer = Auth::user()->rescuer;
        
        if (!$rescuer) {
            return redirect()->back()->with('error', 'Rescuer not found.');
        }

        $rescuer->update([
            'adoption_fee' => $request->adoption_fee,
            'bank_name' => $request->bank_name,
            'bank_account_name' => $request->bank_account_name,
            'bank_account_number' => $request->bank_account_number,
        ]);

        return redirect()->back()->with('success', 'Payment settings updated successfully.');
    }
} 