<?php

namespace App\Console\Commands;

use App\Models\Shared\MayaTransaction;
use App\Services\MayaDisbursementService;
use Illuminate\Console\Command;

class TestPayouts extends Command
{
    protected $signature = 'payouts:test {--transaction-id= : Specific transaction ID to test}';
    protected $description = 'Test payout processing (bypasses 24-hour delay)';

    protected $disbursementService;

    public function __construct(MayaDisbursementService $disbursementService)
    {
        parent::__construct();
        $this->disbursementService = $disbursementService;
    }

    public function handle()
    {
        $this->info('🧪 Testing Maya Disbursement System...');
        $this->newLine();

        // Get paid transactions
        $transactions = MayaTransaction::where('payment_status', 'paid')
            ->where('payout_status', 'pending')
            ->with(['application.pet', 'shelter', 'rescuer'])
            ->get();

        if ($transactions->isEmpty()) {
            $this->error('No paid transactions found for testing.');
            return 1;
        }

        $this->info("Found {$transactions->count()} paid transactions:");
        $this->newLine();

        // Display transactions
        $headers = ['ID', 'Pet', 'Provider', 'Amount', 'Payment Date', 'Bank Details'];
        $rows = [];

        foreach ($transactions as $transaction) {
            $provider = $transaction->shelter ?? $transaction->rescuer;
            $bankDetails = $provider ? 
                ($provider->bank_name . ' - ' . $provider->bank_account_number) : 
                'No bank details';

            $rows[] = [
                $transaction->transaction_id,
                $transaction->application->pet->name,
                $transaction->provider_name,
                '₱' . number_format($transaction->provider_amount, 2),
                $transaction->payment_date->format('M d, Y H:i'),
                $bankDetails
            ];
        }

        $this->table($headers, $rows);
        $this->newLine();

        // Test specific transaction or all
        $transactionId = $this->option('transaction-id');
        
        if ($transactionId) {
            $transaction = $transactions->where('transaction_id', $transactionId)->first();
            if (!$transaction) {
                $this->error("Transaction #{$transactionId} not found.");
                return 1;
            }
            $this->testTransaction($transaction);
        } else {
            $this->info('Choose a transaction to test:');
            foreach ($transactions as $transaction) {
                $this->line("  {$transaction->transaction_id} - {$transaction->application->pet->name} (₱" . number_format($transaction->provider_amount, 2) . ")");
            }
            $this->newLine();
            
            $choice = $this->ask('Enter transaction ID to test (or press Enter to test all):');
            
            if ($choice) {
                $transaction = $transactions->where('transaction_id', $choice)->first();
                if ($transaction) {
                    $this->testTransaction($transaction);
                } else {
                    $this->error("Transaction #{$choice} not found.");
                }
            } else {
                $this->testAllTransactions($transactions);
            }
        }

        return 0;
    }

    protected function testTransaction($transaction)
    {
        $this->info("🧪 Testing payout for transaction #{$transaction->transaction_id}");
        $this->line("Pet: {$transaction->application->pet->name}");
        $this->line("Provider: {$transaction->provider_name}");
        $this->line("Amount: ₱" . number_format($transaction->provider_amount, 2));
        $this->newLine();

        // Check eligibility (bypassing time delay)
        $provider = $transaction->shelter ?? $transaction->rescuer;
        
        if (!$provider) {
            $this->error("❌ Provider not found");
            return;
        }

        if (empty($provider->bank_name) || empty($provider->bank_account_number)) {
            $this->error("❌ Provider missing bank details");
            $this->line("Bank Name: " . ($provider->bank_name ?? 'Not set'));
            $this->line("Account Number: " . ($provider->bank_account_number ?? 'Not set'));
            return;
        }

        $this->info("✅ Provider bank details validated");
        $this->line("Bank: {$provider->bank_name}");
        $this->line("Account: {$provider->bank_account_number}");
        $this->line("Account Name: {$provider->bank_account_name}");
        $this->newLine();

        // Simulate payout processing
        $this->info("🔄 Simulating Maya API call...");
        
        // In a real scenario, this would call the Maya API
        // For testing, we'll just update the status
        $transaction->update([
            'payout_status' => 'processing',
            'payout_reference' => 'TEST-' . time(),
            'payout_date' => now()
        ]);

        $this->info("✅ Payout status updated to 'processing'");
        $this->info("📧 Email notification would be sent to: " . ($provider->user->email ?? 'No email'));
        $this->newLine();

        $this->info("🎉 Test completed! Transaction #{$transaction->transaction_id} is now processing.");
    }

    protected function testAllTransactions($transactions)
    {
        $this->info("🧪 Testing all {$transactions->count()} transactions...");
        $this->newLine();

        $successCount = 0;
        $errorCount = 0;

        foreach ($transactions as $transaction) {
            $this->line("Testing transaction #{$transaction->transaction_id}...");
            
            try {
                $provider = $transaction->shelter ?? $transaction->rescuer;
                
                if (!$provider || empty($provider->bank_name) || empty($provider->bank_account_number)) {
                    $this->error("  ❌ Missing bank details");
                    $errorCount++;
                    continue;
                }

                // Simulate processing
                $transaction->update([
                    'payout_status' => 'processing',
                    'payout_reference' => 'TEST-' . time() . '-' . $transaction->transaction_id,
                    'payout_date' => now()
                ]);

                $this->info("  ✅ Processed");
                $successCount++;
                
            } catch (\Exception $e) {
                $this->error("  ❌ Error: " . $e->getMessage());
                $errorCount++;
            }
        }

        $this->newLine();
        $this->info("🎉 Test Results:");
        $this->line("  ✅ Successful: {$successCount}");
        $this->line("  ❌ Errors: {$errorCount}");
        $this->line("  📊 Total: {$transactions->count()}");
    }
} 