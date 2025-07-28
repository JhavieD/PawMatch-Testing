<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\MayaPaymentService;
use App\Models\Shared\AdoptionApplication;
use App\Models\Shared\MayaTransaction;

class PaymentController extends Controller
{
    protected $mayaService;

    public function __construct(MayaPaymentService $mayaService)
    {
        $this->mayaService = $mayaService;
    }

    /**
     * Show payment form for adoption application
     */
    public function showPaymentForm($applicationId)
    {
        $application = AdoptionApplication::with(['adopter.user', 'pet', 'shelter', 'rescuer'])
            ->findOrFail($applicationId);

        // Check if user is authorized to pay for this application
        if (Auth::user()->adopter->adopter_id !== $application->adopter_id) {
            abort(403, 'Unauthorized access to payment.');
        }

        // Check if application is approved
        if ($application->status !== 'approved') {
            return redirect()->back()->with('error', 'Application must be approved before payment.');
        }

        // Check if payment is already completed
        if ($application->payment_status === 'paid') {
            return redirect()->route('adopter.application-status')
                ->with('info', 'Payment already completed for this application.');
        }

        // Get adoption fee from provider
        $adoptionFee = 0;
        if ($application->shelter) {
            $adoptionFee = $application->shelter->adoption_fee;
        } elseif ($application->rescuer) {
            $adoptionFee = $application->rescuer->adoption_fee;
        }

        if ($adoptionFee <= 0) {
            return redirect()->back()->with('error', 'No adoption fee set for this pet.');
        }

        // Update application with payment amount if not already set
        if (!$application->payment_amount) {
            $application->update([
                'payment_amount' => $adoptionFee,
                'payment_status' => 'pending'
            ]);
        }

        return view('payment.adoption-payment', compact('application', 'adoptionFee'));
    }

    /**
     * Process payment and create Maya checkout
     */
    public function processPayment(Request $request, $applicationId)
    {
        $application = AdoptionApplication::with(['adopter.user', 'pet', 'shelter', 'rescuer'])
            ->findOrFail($applicationId);

        // Check authorization
        if (Auth::user()->adopter->adopter_id !== $application->adopter_id) {
            abort(403, 'Unauthorized access to payment.');
        }

        // Validate application status
        if ($application->status !== 'approved') {
            return response()->json(['error' => 'Application must be approved before payment.'], 400);
        }

        if ($application->payment_status === 'paid') {
            return response()->json(['error' => 'Payment already completed.'], 400);
        }

        // Get adoption fee
        $adoptionFee = 0;
        if ($application->shelter) {
            $adoptionFee = $application->shelter->adoption_fee;
        } elseif ($application->rescuer) {
            $adoptionFee = $application->rescuer->adoption_fee;
        }

        if ($adoptionFee <= 0) {
            return response()->json(['error' => 'No adoption fee set for this pet.'], 400);
        }

        // Create Maya checkout
        $result = $this->mayaService->createCheckout($application, $adoptionFee);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'redirect_url' => $result['redirect_url'],
                'checkout_id' => $result['checkout_id']
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $result['error']
        ], 400);
    }

    /**
     * Payment success callback
     */
    public function paymentSuccess(Request $request, $applicationId)
    {
        $application = AdoptionApplication::findOrFail($applicationId);
        $isTestMode = $request->get('test_mode') === '1';
        $amount = $request->get('amount', 0);
        
        // If amount is 0, get the adoption fee from the provider
        if ($amount == 0) {
            if ($application->pet->shelter) {
                $amount = $application->pet->shelter->adoption_fee;
            } elseif ($application->pet->rescuer) {
                $amount = $application->pet->rescuer->adoption_fee;
            }
        }
        
        // Update application status to allow meet & greet
        $application->payment_status = 'paid';
        $application->payment_amount = $amount;
        $application->payment_date = now();
        $application->save();

        // Update the corresponding MayaTransaction record
        $transaction = MayaTransaction::where('application_id', $applicationId)
            ->where('payment_status', 'pending')
            ->first();
            
        if ($transaction) {
            $transaction->payment_status = 'paid';
            $transaction->payment_date = now();
            $transaction->save();
        }

        $message = $isTestMode 
            ? 'Test payment completed successfully! You can now proceed with the meet & greet.'
            : 'Payment completed successfully! You can now proceed with the meet & greet.';

        return view('payment.success', compact('application'))
            ->with('success', $message);
    }

    /**
     * Payment failure callback
     */
    public function paymentFailure(Request $request, $applicationId)
    {
        $application = AdoptionApplication::findOrFail($applicationId);
        
        // Update application payment status
        $application->payment_status = 'failed';
        $application->save();

        // Update the corresponding MayaTransaction record
        $transaction = MayaTransaction::where('application_id', $applicationId)
            ->where('payment_status', 'pending')
            ->first();
            
        if ($transaction) {
            $transaction->payment_status = 'failed';
            $transaction->save();
        }

        return view('payment.failure', compact('application'))
            ->with('error', 'Payment failed. Please try again.');
    }

    /**
     * Payment cancellation callback
     */
    public function paymentCancel(Request $request, $applicationId)
    {
        $application = AdoptionApplication::findOrFail($applicationId);
        
        return view('payment.cancel', compact('application'))
            ->with('info', 'Payment was cancelled. You can try again later.');
    }

    /**
     * Maya webhook endpoint
     */
    public function webhook(Request $request)
    {
        $payload = $request->all();
        $signature = $request->header('X-Maya-Signature');

        // Process webhook
        $result = $this->mayaService->handleWebhook($payload, $signature);

        if ($result) {
            return response()->json(['status' => 'success'], 200);
        }

        return response()->json(['status' => 'error'], 400);
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus($applicationId)
    {
        $application = AdoptionApplication::findOrFail($applicationId);
        
        // Check authorization
        if (Auth::user()->adopter->adopter_id !== $application->adopter_id) {
            abort(403, 'Unauthorized access.');
        }

        $transaction = MayaTransaction::where('application_id', $applicationId)
            ->latest()
            ->first();

        if (!$transaction) {
            return response()->json(['status' => 'no_transaction']);
        }

        // Check with Maya API
        if ($transaction->maya_payment_id) {
            $mayaStatus = $this->mayaService->getPaymentStatus($transaction->maya_payment_id);
            if ($mayaStatus) {
                $transaction->payment_status = $this->mayaService->mapMayaStatus($mayaStatus['status'] ?? 'pending');
                $transaction->save();
            }
        }

        return response()->json([
            'status' => $transaction->payment_status,
            'amount' => $transaction->total_amount,
            'payment_date' => $transaction->payment_date
        ]);
    }

    /**
     * Show payment history for the authenticated user
     */
    public function paymentHistory()
    {
        $user = Auth::user();
        $transactions = collect();

        if ($user->role === 'adopter') {
            if (!$user->adopter) {
                abort(403, 'Adopter profile not found.');
            }
            $transactions = MayaTransaction::with(['application.pet', 'shelter', 'rescuer'])
                ->where('adopter_id', $user->adopter->adopter_id)
                ->orderBy('payment_date', 'desc')
                ->paginate(10);
        } elseif ($user->role === 'shelter') {
            if (!$user->shelter) {
                abort(403, 'Shelter profile not found.');
            }
            $transactions = MayaTransaction::with(['application.pet', 'adopter.user'])
                ->where('shelter_id', $user->shelter->shelter_id)
                ->orderBy('payment_date', 'desc')
                ->paginate(10);
        } elseif ($user->role === 'rescuer') {
            if (!$user->rescuer) {
                abort(403, 'Rescuer profile not found.');
            }
            $transactions = MayaTransaction::with(['application.pet', 'adopter.user'])
                ->where('rescuer_id', $user->rescuer->rescuer_id)
                ->orderBy('payment_date', 'desc')
                ->paginate(10);
        } else {
            abort(403, 'Invalid user role for payment history.');
        }

        // Return appropriate view based on user role
        if ($user->role === 'shelter') {
            return view('shelter.transaction-history', compact('transactions'));
        } elseif ($user->role === 'rescuer') {
            return view('rescuer.transaction-history', compact('transactions'));
        } else {
            return view('adopter.transaction-history', compact('transactions'));
        }
    }

    /**
     * Get transaction details for modal
     */
    public function getTransactionDetails($transactionId)
    {
        $transaction = MayaTransaction::with(['application.pet', 'shelter', 'rescuer', 'adopter.user'])
            ->findOrFail($transactionId);

        $html = view('payment.transaction-details', compact('transaction'))->render();

        return response()->json(['success' => true, 'html' => $html]);
    }

    /**
     * Show test payment page (for development)
     */
    public function testPayment()
    {
        $testCards = $this->mayaService->getTestCards();
        $testWallets = $this->mayaService->getTestWallets();

        return view('payment.test', compact('testCards', 'testWallets'));
    }
} 