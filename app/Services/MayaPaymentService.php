<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Shared\MayaTransaction;
use App\Models\Shared\AdoptionApplication;

class MayaPaymentService
{
    protected $config;
    protected $baseUrl;
    protected $publicKey;
    protected $secretKey;

    public function __construct()
    {
        $this->config = config('maya');
        $environment = $this->config['environment'];
        $this->baseUrl = $this->config[$environment]['base_url'];
        $this->publicKey = $this->config[$environment]['public_key'];
        $this->secretKey = $this->config[$environment]['secret_key'];
    }

    /**
     * Create a Maya Checkout session for adoption payment
     */
    public function createCheckout(AdoptionApplication $application, $amount)
    {
        try {
            $checkoutData = [
                'totalAmount' => [
                    'value' => $amount,
                    'currency' => 'PHP'
                ],
                'requestReferenceNumber' => 'PAWMATCH-' . $application->application_id . '-' . time(),
                'items' => [
                    [
                        'name' => 'Adoption Fee - ' . ($application->pet->name ?? 'Pet'),
                        'quantity' => 1,
                        'code' => 'ADOPTION-FEE',
                        'amount' => [
                            'value' => $amount,
                            'currency' => 'PHP'
                        ],
                        'totalAmount' => [
                            'value' => $amount,
                            'currency' => 'PHP'
                        ]
                    ]
                ],
                'redirectUrl' => [
                    'success' => route('payment.success', ['applicationId' => $application->application_id]),
                    'failure' => route('payment.failure', ['applicationId' => $application->application_id]),
                    'cancel' => route('payment.cancel', ['applicationId' => $application->application_id])
                ],
                'buyer' => [
                    'firstName' => $application->adopter->user->name ?? 'Adopter',
                    'email' => $application->adopter->user->email ?? '',
                    'phone' => $application->adopter->user->phone ?? ''
                ],
                'paymentMethod' => [
                    'enabledPaymentMethods' => ['CARD', 'MAYA_WALLET', 'QR_CODE']
                ]
            ];

            // Log the request data for debugging
            Log::info('Maya Checkout Request', [
                'application_id' => $application->application_id,
                'checkout_data' => $checkoutData,
                'url' => $this->baseUrl . '/checkout/v1/checkouts'
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->publicKey . ':' . $this->secretKey),
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/checkout/v1/checkouts', $checkoutData);

            if ($response->successful()) {
                $checkoutData = $response->json();
                
                // Create transaction record
                $transaction = MayaTransaction::create([
                    'application_id' => $application->application_id,
                    'adopter_id' => $application->adopter_id,
                    'shelter_id' => $application->shelter_id,
                    'rescuer_id' => $application->rescuer_id,
                    'total_amount' => $amount,
                    'pawmatch_commission' => $this->calculateCommission($amount),
                    'provider_amount' => $this->calculateProviderAmount($amount),
                    'maya_payment_id' => $checkoutData['checkoutId'] ?? null,
                    'payment_status' => 'pending',
                    'maya_response' => $checkoutData
                ]);

                return [
                    'success' => true,
                    'checkout_id' => $checkoutData['checkoutId'] ?? null,
                    'redirect_url' => $checkoutData['redirectUrl'] ?? null,
                    'transaction_id' => $transaction->transaction_id
                ];
            }

            Log::error('Maya Checkout creation failed', [
                'application_id' => $application->application_id,
                'response' => $response->json(),
                'status' => $response->status()
            ]);

            return [
                'success' => false,
                'error' => 'Failed to create checkout session',
                'details' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('Maya Checkout creation exception', [
                'application_id' => $application->application_id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Payment service error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Retrieve payment status from Maya
     */
    public function getPaymentStatus($checkoutId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->publicKey . ':' . $this->secretKey),
                'Content-Type' => 'application/json'
            ])->get($this->baseUrl . '/checkout/v1/checkouts/' . $checkoutId);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Maya payment status check failed', [
                'checkout_id' => $checkoutId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Handle webhook notifications from Maya
     */
    public function handleWebhook($payload, $signature)
    {
        try {
            // Verify webhook signature (implement based on Maya's webhook verification)
            // if (!$this->verifyWebhookSignature($payload, $signature)) {
            //     return false;
            // }

            $checkoutId = $payload['checkoutId'] ?? null;
            $status = $payload['status'] ?? null;

            if (!$checkoutId || !$status) {
                Log::error('Invalid webhook payload', ['payload' => $payload]);
                return false;
            }

            // Find transaction by Maya payment ID
            $transaction = MayaTransaction::where('maya_payment_id', $checkoutId)->first();

            if (!$transaction) {
                Log::error('Transaction not found for webhook', ['checkout_id' => $checkoutId]);
                return false;
            }

            // Update transaction status
            $transaction->payment_status = $this->mapMayaStatus($status);
            $transaction->payment_date = now();
            $transaction->maya_response = array_merge($transaction->maya_response ?? [], $payload);
            $transaction->save();

            // Update application payment status
            $application = $transaction->application;
            if ($application) {
                $application->payment_status = $this->mapMayaStatus($status);
                $application->payment_amount = $transaction->total_amount;
                $application->payment_date = now();
                $application->save();
            }

            Log::info('Webhook processed successfully', [
                'checkout_id' => $checkoutId,
                'status' => $status,
                'transaction_id' => $transaction->transaction_id
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'payload' => $payload,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Calculate PawMatch commission (20%)
     */
    public function calculateCommission($amount)
    {
        return $amount * ($this->config['commission_percentage'] / 100);
    }

    /**
     * Calculate provider amount (80%)
     */
    public function calculateProviderAmount($amount)
    {
        return $amount - $this->calculateCommission($amount);
    }

    /**
     * Map Maya payment status to internal status
     */
    public function mapMayaStatus($mayaStatus)
    {
        $statusMap = [
            'COMPLETED' => 'paid',
            'PENDING' => 'pending',
            'FAILED' => 'failed',
            'CANCELLED' => 'failed',
            'EXPIRED' => 'failed'
        ];

        return $statusMap[$mayaStatus] ?? 'pending';
    }

    /**
     * Get test card details for sandbox testing
     */
    public function getTestCards()
    {
        return $this->config['test_cards'] ?? [];
    }

    /**
     * Get test wallet details for sandbox testing
     */
    public function getTestWallets()
    {
        return $this->config['test_wallets'] ?? [];
    }
} 