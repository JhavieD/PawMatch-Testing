<?php

namespace App\Services;

use App\Models\Shared\MayaTransaction;
use App\Models\Shelter\Shelter;
use App\Models\Rescuer\Rescuer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PayoutNotification;

class MayaDisbursementService
{
    protected $baseUrl;
    protected $secretKey;
    protected $publicKey;
    protected $environment;

    public function __construct()
    {
        $this->environment = config('maya.environment');
        $config = config("maya.{$this->environment}");
        
        $this->baseUrl = $config['disbursement_url'];
        $this->secretKey = $config['secret_key'];
        $this->publicKey = $config['public_key'];
    }

    /**
     * Process automatic payout for a transaction
     */
    public function processPayout(MayaTransaction $transaction)
    {
        try {
            // Check if disbursement is enabled
            if (!config('maya.disbursement.enabled')) {
                Log::info('Maya disbursement is disabled');
                return false;
            }

            // Check if transaction is eligible for payout
            if (!$this->isEligibleForPayout($transaction)) {
                Log::info("Transaction {$transaction->transaction_id} is not eligible for payout");
                return false;
            }

            // Get provider details
            $provider = $this->getProvider($transaction);
            if (!$provider) {
                Log::error("Provider not found for transaction {$transaction->transaction_id}");
                return false;
            }

            // Validate provider bank details
            if (!$this->validateBankDetails($provider)) {
                Log::error("Invalid bank details for provider in transaction {$transaction->transaction_id}");
                $this->updatePayoutStatus($transaction, 'failed', 'Invalid bank details');
                return false;
            }

            // TEST MODE: Simulate successful payout for development
            if (app()->environment('local') || config('maya.disbursement.test_mode', false)) {
                Log::info("TEST MODE: Simulating successful payout for transaction {$transaction->transaction_id}");
                
                // Simulate processing delay
                sleep(1);
                
                // Update status to completed (simulated)
                $this->updatePayoutStatus($transaction, 'completed', null, 'TEST-' . uniqid());
                
                // Send notification
                $this->sendPayoutNotification($transaction);
                
                Log::info("TEST MODE: Payout completed for transaction {$transaction->transaction_id}");
                return true;
            }

            // Create disbursement request
            $disbursementData = $this->createDisbursementRequest($transaction, $provider);
            
            // Send request to Maya API
            $response = $this->sendDisbursementRequest($disbursementData);
            
            if ($response['success']) {
                $this->updatePayoutStatus($transaction, 'processing', null, $response['reference']);
                Log::info("Payout initiated for transaction {$transaction->transaction_id}");
                return true;
            } else {
                $this->updatePayoutStatus($transaction, 'failed', $response['error']);
                Log::error("Payout failed for transaction {$transaction->transaction_id}: {$response['error']}");
                return false;
            }

        } catch (\Exception $e) {
            Log::error("Error processing payout for transaction {$transaction->transaction_id}: " . $e->getMessage());
            $this->updatePayoutStatus($transaction, 'failed', $e->getMessage());
            return false;
        }
    }

    /**
     * Check if transaction is eligible for payout
     */
    protected function isEligibleForPayout(MayaTransaction $transaction)
    {
        // Check if payment is completed
        if ($transaction->payment_status !== 'paid') {
            return false;
        }

        // Check if payout is not already processed
        if (in_array($transaction->payout_status, ['processing', 'completed'])) {
            return false;
        }

        // Check if auto payout is enabled
        if (!config('maya.disbursement.auto_payout')) {
            return false;
        }

        // Check payout delay (skip in test mode)
        if (!config('maya.disbursement.test_mode', false)) {
            $payoutDelay = config('maya.disbursement.payout_delay_hours', 24);
            $payoutTime = $transaction->payment_date->addHours($payoutDelay);
            
            if (now()->lt($payoutTime)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get provider (shelter or rescuer) for the transaction
     */
    protected function getProvider(MayaTransaction $transaction)
    {
        if ($transaction->shelter_id) {
            return Shelter::find($transaction->shelter_id);
        }
        
        if ($transaction->rescuer_id) {
            return Rescuer::find($transaction->rescuer_id);
        }
        
        return null;
    }

    /**
     * Validate provider bank details
     */
    protected function validateBankDetails($provider)
    {
        return !empty($provider->bank_name) && 
               !empty($provider->bank_account_number) && 
               !empty($provider->bank_account_name);
    }

    /**
     * Create disbursement request data
     */
    protected function createDisbursementRequest(MayaTransaction $transaction, $provider)
    {
        return [
            'amount' => [
                'value' => $transaction->provider_amount,
                'currency' => 'PHP'
            ],
            'recipient' => [
                'firstName' => $this->extractFirstName($provider->bank_account_name),
                'lastName' => $this->extractLastName($provider->bank_account_name),
                'email' => $provider->user->email ?? '',
                'phone' => $provider->contact_number ?? ''
            ],
            'bankAccount' => [
                'bankCode' => $this->getBankCode($provider->bank_name),
                'accountNumber' => $provider->bank_account_number,
                'accountName' => $provider->bank_account_name
            ],
            'referenceId' => "PAWMATCH-{$transaction->transaction_id}",
            'description' => "Payout for adoption of {$transaction->application->pet->name}",
            'metadata' => [
                'transaction_id' => $transaction->transaction_id,
                'provider_type' => $transaction->provider_type,
                'provider_id' => $transaction->shelter_id ?? $transaction->rescuer_id
            ]
        ];
    }

    /**
     * Send disbursement request to Maya API
     */
    protected function sendDisbursementRequest($data)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->secretKey . ':'),
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/disbursements', $data);

            if ($response->successful()) {
                $responseData = $response->json();
                return [
                    'success' => true,
                    'reference' => $responseData['id'] ?? null,
                    'data' => $responseData
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->body(),
                    'status' => $response->status()
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Handle disbursement webhook
     */
    public function handleWebhook($payload, $signature)
    {
        try {
            // Verify webhook signature
            if (!$this->verifyWebhookSignature($payload, $signature)) {
                Log::error('Invalid webhook signature');
                return false;
            }

            $disbursementId = $payload['id'] ?? null;
            $status = $payload['status'] ?? null;
            $referenceId = $payload['referenceId'] ?? null;

            if (!$disbursementId || !$status || !$referenceId) {
                Log::error('Invalid webhook payload');
                return false;
            }

            // Extract transaction ID from reference
            $transactionId = $this->extractTransactionId($referenceId);
            if (!$transactionId) {
                Log::error("Could not extract transaction ID from reference: {$referenceId}");
                return false;
            }

            // Find and update transaction
            $transaction = MayaTransaction::find($transactionId);
            if (!$transaction) {
                Log::error("Transaction not found: {$transactionId}");
                return false;
            }

            // Update payout status
            $this->updatePayoutStatus($transaction, $status, null, $disbursementId);

            // Send notification email
            if ($status === 'completed') {
                $this->sendPayoutNotification($transaction);
            }

            Log::info("Webhook processed for disbursement {$disbursementId}, status: {$status}");
            return true;

        } catch (\Exception $e) {
            Log::error('Error processing disbursement webhook: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update payout status
     */
    protected function updatePayoutStatus(MayaTransaction $transaction, $status, $error = null, $reference = null)
    {
        $transaction->payout_status = $status;
        
        if ($status === 'completed') {
            $transaction->payout_date = now();
        }
        
        if ($reference) {
            $transaction->payout_reference = $reference;
        }

        if ($error) {
            $transaction->notes = $error;
        }

        $transaction->save();
    }

    /**
     * Send payout notification email
     */
    protected function sendPayoutNotification(MayaTransaction $transaction)
    {
        try {
            $provider = $this->getProvider($transaction);
            if ($provider && $provider->user) {
                Mail::to($provider->user->email)->send(new PayoutNotification($transaction));
            }
        } catch (\Exception $e) {
            Log::error('Error sending payout notification: ' . $e->getMessage());
        }
    }

    /**
     * Helper methods
     */
    protected function extractFirstName($fullName)
    {
        $parts = explode(' ', trim($fullName));
        return $parts[0] ?? '';
    }

    protected function extractLastName($fullName)
    {
        $parts = explode(' ', trim($fullName));
        return count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';
    }

    protected function getBankCode($bankName)
    {
        // Map bank names to Maya bank codes
        $bankCodes = [
            'BDO' => 'BDO',
            'BPI' => 'BPI',
            'Metrobank' => 'MBTC',
            'Unionbank' => 'UBP',
            'Landbank' => 'LBP',
            'RCBC' => 'RCBC',
            'PNB' => 'PNB',
            'Security Bank' => 'SBC',
            'EastWest Bank' => 'EWBC',
            'Chinabank' => 'CBC',
            'UCPB' => 'UCPB',
            'PSBank' => 'PSB',
            'Robinsons Bank' => 'RBC',
            'Maybank' => 'MBTC',
            'CIMB' => 'CIMB',
            'GCash' => 'GCASH',
            'PayMaya' => 'PAYMAYA'
        ];

        foreach ($bankCodes as $name => $code) {
            if (stripos($bankName, $name) !== false) {
                return $code;
            }
        }

        return 'OTHERS'; // Default for unknown banks
    }

    protected function extractTransactionId($referenceId)
    {
        if (preg_match('/PAWMATCH-(\d+)/', $referenceId, $matches)) {
            return $matches[1];
        }
        return null;
    }

    protected function verifyWebhookSignature($payload, $signature)
    {
        $webhookSecret = config('maya.disbursement.webhook_secret');
        if (empty($webhookSecret)) {
            return true; // Skip verification if no secret configured
        }

        $expectedSignature = hash_hmac('sha256', json_encode($payload), $webhookSecret);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Get pending payouts for admin dashboard
     */
    public function getPendingPayouts()
    {
        return MayaTransaction::where('payment_status', 'paid')
            ->where('payout_status', 'pending')
            ->with(['application.pet', 'shelter', 'rescuer'])
            ->get();
    }

    /**
     * Get payout statistics
     */
    public function getPayoutStats()
    {
        return [
            'total_payouts' => MayaTransaction::where('payout_status', 'completed')->count(),
            'pending_payouts' => MayaTransaction::where('payout_status', 'pending')->count(),
            'failed_payouts' => MayaTransaction::where('payout_status', 'failed')->count(),
            'total_amount_paid' => MayaTransaction::where('payout_status', 'completed')->sum('provider_amount'),
            'pending_amount' => MayaTransaction::where('payout_status', 'pending')->sum('provider_amount'),
        ];
    }
} 