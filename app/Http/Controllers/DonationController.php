<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Services\MayaPaymentService;
use App\Models\Shared\Donation;
use App\Mail\DonationThankYou;

class DonationController extends Controller
{
    protected MayaPaymentService $mayaService;

    public function __construct(MayaPaymentService $mayaService)
    {
        $this->mayaService = $mayaService;
    }

    public function showForm()
    {
        $user = Auth::user();
        return view('donate', compact('user'));
    }

    public function process(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'amount' => ['required', 'numeric', 'min:10'],
            'donor_name' => ['nullable', 'string', 'max:255'],
            'donor_email' => ['nullable', 'email', 'max:255'],
            'message' => ['nullable', 'string', 'max:2000'],
        ];
        if (!$user) {
            $rules['donor_email'][0] = 'required'; // require email for guests
        }

        $validated = $request->validate($rules);

        $donation = Donation::create([
            'user_id' => $user?->user_id,
            'donor_name' => $validated['donor_name'] ?? ($user ? ($user->first_name . ' ' . $user->last_name) : null),
            'donor_email' => $validated['donor_email'] ?? ($user?->email ?? null),
            'amount' => $validated['amount'],
            'message' => $validated['message'] ?? null,
            'payment_status' => 'pending',
        ]);

        $result = $this->mayaService->createDonationCheckout($donation);

        if (($result['success'] ?? false) && !empty($result['redirect_url'])) {
            // Save checkout id
            $donation->maya_payment_id = $result['checkout_id'] ?? null;
            $donation->maya_response = [
                'checkout_id' => $result['checkout_id'] ?? null,
                'redirect_url' => $result['redirect_url'] ?? null,
            ];
            $donation->save();

            return redirect()->away($result['redirect_url']);
        }

        Log::error('Donation checkout creation failed', ['result' => $result]);
        return back()->withErrors(['amount' => 'Unable to initiate payment. Please try again.']);
    }

    public function success(Request $request)
    {
        $checkoutId = $request->query('checkoutId') ?? $request->query('paymentId');
        $donationId = $request->query('donation_id');

        $donation = null;
        if ($donationId) {
            $donation = Donation::find($donationId);
        }
        if (!$donation && $checkoutId) {
            $donation = Donation::where('maya_payment_id', $checkoutId)->first();
        }

        if (!$donation) {
            return view('payment.donation-success')->with('error', 'Donation not found, but payment may have succeeded.');
        }

        if ($donation->payment_status !== 'paid') {
            $donation->payment_status = 'paid';
            $donation->payment_date = now();
            $donation->save();
        }

        // Send thank-you email once
        $this->sendThankYouEmailIfNeeded($donation);

        return view('payment.donation-success', compact('donation'))->with('success', 'Thank you for your donation!');
    }

    public function failure(Request $request)
    {
        $checkoutId = $request->query('checkoutId') ?? $request->query('paymentId');
        $donationId = $request->query('donation_id');

        $donation = null;
        if ($donationId) {
            $donation = Donation::find($donationId);
        }
        if (!$donation && $checkoutId) {
            $donation = Donation::where('maya_payment_id', $checkoutId)->first();
        }

        if ($donation) {
            $donation->payment_status = 'failed';
            $donation->save();
        }
        return view('payment.donation-failure', compact('donation'));
    }

    public function cancel(Request $request)
    {
        $checkoutId = $request->query('checkoutId') ?? $request->query('paymentId');
        $donationId = $request->query('donation_id');

        $donation = null;
        if ($donationId) {
            $donation = Donation::find($donationId);
        }
        if (!$donation && $checkoutId) {
            $donation = Donation::where('maya_payment_id', $checkoutId)->first();
        }

        return view('payment.donation-cancel', compact('donation'));
    }

    public function webhook(Request $request)
    {
        $payload = $request->all();
        $signature = $request->header('X-Maya-Signature');

        // Basic processing without signature verification demo
        $checkoutId = $payload['checkoutId'] ?? null;
        $status = $payload['status'] ?? null;

        if (!$checkoutId || !$status) {
            return response()->json(['status' => 'invalid'], 400);
        }

        $donation = Donation::where('maya_payment_id', $checkoutId)->first();
        if ($donation) {
            $donation->payment_status = match ($status) {
                'COMPLETED' => 'paid',
                'FAILED', 'CANCELLED', 'EXPIRED' => 'failed',
                default => 'pending',
            };
            if ($donation->payment_status === 'paid' && !$donation->payment_date) {
                $donation->payment_date = now();
            }
            $donation->maya_response = array_merge($donation->maya_response ?? [], $payload);
            $donation->save();

            if ($donation->payment_status === 'paid') {
                $this->sendThankYouEmailIfNeeded($donation);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    private function sendThankYouEmailIfNeeded(Donation $donation): void
    {
        try {
            if (!$donation->donor_email) { return; }
            if ($donation->thank_you_sent_at) { return; }

            // Send synchronously to avoid requiring a queue worker
            Mail::to($donation->donor_email)->send(new DonationThankYou($donation));
            $donation->thank_you_sent_at = now();
            $donation->save();

            Log::info('Donation thank-you email sent', [
                'donation_id' => $donation->donation_id,
                'email' => $donation->donor_email,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to send donation thank-you email', [
                'donation_id' => $donation->donation_id,
                'error' => $e->getMessage(),
            ]);
            try { Mail::mailer('log')->to($donation->donor_email)->send(new DonationThankYou($donation)); } catch (\Throwable $ignored) {}
        }
    }
} 