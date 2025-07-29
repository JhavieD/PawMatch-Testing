<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Shared\MayaTransaction;
use App\Models\Shared\AdoptionApplication;
use App\Models\Shelter\Shelter;
use App\Models\Rescuer\Rescuer;
use Carbon\Carbon;

class PaymentDashboardController extends Controller
{
    /**
     * Show all transactions with filtering
     */
    public function transactions(Request $request)
    {
        $query = MayaTransaction::with(['application.pet', 'shelter', 'rescuer', 'adopter.user']);
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }
        
        if ($request->filled('provider_type')) {
            if ($request->provider_type === 'shelter') {
                $query->whereNotNull('shelter_id');
            } elseif ($request->provider_type === 'rescuer') {
                $query->whereNotNull('rescuer_id');
            }
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }
        
        $transactions = $query->orderBy('payment_date', 'desc')->paginate(20);
        
        return view('admin.transactions', compact('transactions'));
    }
    
    /**
     * Show payout management
     */
    public function payouts()
    {
        $pendingPayouts = MayaTransaction::where('payment_status', 'paid')
            ->where('payout_status', 'pending')
            ->with(['application.pet', 'shelter', 'rescuer'])
            ->orderBy('payment_date', 'asc')
            ->get();
            
        $completedPayouts = MayaTransaction::where('payment_status', 'paid')
            ->where('payout_status', 'completed')
            ->with(['application.pet', 'shelter', 'rescuer'])
            ->orderBy('payout_date', 'desc')
            ->limit(20)
            ->get();
            
        return view('admin.payouts', compact('pendingPayouts', 'completedPayouts'));
    }
    
    /**
     * Process manual payout
     */
    public function processPayout(Request $request, $transactionId)
    {
        $transaction = MayaTransaction::findOrFail($transactionId);
        
        // Update payout status
        $transaction->payout_status = 'completed';
        $transaction->payout_date = now();
        $transaction->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Payout processed successfully'
        ]);
    }
    
    /**
     * Get transaction details for modal
     */
    public function getTransactionDetails($transactionId)
    {
        try {
            $transaction = MayaTransaction::with(['application.pet', 'shelter', 'rescuer', 'adopter.user'])
                ->findOrFail($transactionId);
            
            $html = view('admin.transaction-details', compact('transaction'))->render();
            
            return response()->json(['success' => true, 'html' => $html]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
} 