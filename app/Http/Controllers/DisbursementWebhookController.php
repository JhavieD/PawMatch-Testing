<?php

namespace App\Http\Controllers;

use App\Services\MayaDisbursementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DisbursementWebhookController extends Controller
{
    protected $disbursementService;

    public function __construct(MayaDisbursementService $disbursementService)
    {
        $this->disbursementService = $disbursementService;
    }

    /**
     * Handle Maya disbursement webhook
     */
    public function handle(Request $request)
    {
        try {
            Log::info('Disbursement webhook received', [
                'payload' => $request->all(),
                'headers' => $request->headers->all()
            ]);

            // Get the webhook signature
            $signature = $request->header('X-Maya-Signature');
            
            if (!$signature) {
                Log::error('No webhook signature found');
                return response()->json(['error' => 'No signature provided'], 400);
            }

            // Process the webhook
            $success = $this->disbursementService->handleWebhook($request->all(), $signature);

            if ($success) {
                Log::info('Disbursement webhook processed successfully');
                return response()->json(['status' => 'success'], 200);
            } else {
                Log::error('Failed to process disbursement webhook');
                return response()->json(['error' => 'Processing failed'], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error processing disbursement webhook: ' . $e->getMessage(), [
                'exception' => $e,
                'payload' => $request->all()
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Test webhook endpoint (for development)
     */
    public function test(Request $request)
    {
        if (app()->environment('production')) {
            return response()->json(['error' => 'Not available in production'], 404);
        }

        $testPayload = [
            'id' => 'test-disbursement-' . time(),
            'status' => 'completed',
            'referenceId' => 'PAWMATCH-1',
            'amount' => [
                'value' => 800.00,
                'currency' => 'PHP'
            ],
            'recipient' => [
                'firstName' => 'Test',
                'lastName' => 'Provider',
                'email' => 'test@example.com'
            ],
            'bankAccount' => [
                'bankCode' => 'BDO',
                'accountNumber' => '1234567890',
                'accountName' => 'Test Provider'
            ],
            'createdAt' => now()->toISOString(),
            'updatedAt' => now()->toISOString()
        ];

        $signature = hash_hmac('sha256', json_encode($testPayload), config('maya.disbursement.webhook_secret', 'test-secret'));

        $request->headers->set('X-Maya-Signature', $signature);

        return $this->handle($request);
    }
} 