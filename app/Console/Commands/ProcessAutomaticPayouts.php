<?php

namespace App\Console\Commands;

use App\Models\Shared\MayaTransaction;
use App\Services\MayaDisbursementService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessAutomaticPayouts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payouts:process {--dry-run : Show what would be processed without actually processing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process automatic payouts for eligible transactions';

    protected $disbursementService;

    /**
     * Create a new command instance.
     */
    public function __construct(MayaDisbursementService $disbursementService)
    {
        parent::__construct();
        $this->disbursementService = $disbursementService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting automatic payout processing...');

        // Check if disbursement is enabled
        if (!config('maya.disbursement.enabled')) {
            $this->error('Maya disbursement is disabled in configuration.');
            return 1;
        }

        // Get eligible transactions
        $eligibleTransactions = $this->getEligibleTransactions();

        if ($eligibleTransactions->isEmpty()) {
            $this->info('No eligible transactions found for payout.');
            return 0;
        }

        $this->info("Found {$eligibleTransactions->count()} eligible transactions for payout.");

        if ($this->option('dry-run')) {
            $this->showDryRunResults($eligibleTransactions);
            return 0;
        }

        // Process payouts
        $successCount = 0;
        $failureCount = 0;

        $progressBar = $this->output->createProgressBar($eligibleTransactions->count());
        $progressBar->start();

        foreach ($eligibleTransactions as $transaction) {
            try {
                $result = $this->disbursementService->processPayout($transaction);
                
                if ($result) {
                    $successCount++;
                    $this->line("\n✅ Processed payout for transaction #{$transaction->transaction_id}");
                } else {
                    $failureCount++;
                    $this->line("\n❌ Failed to process payout for transaction #{$transaction->transaction_id}");
                }
            } catch (\Exception $e) {
                $failureCount++;
                $this->line("\n❌ Error processing transaction #{$transaction->transaction_id}: " . $e->getMessage());
                Log::error("Error in automatic payout processing: " . $e->getMessage());
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Show results
        $this->info("Payout processing completed!");
        $this->table(
            ['Status', 'Count'],
            [
                ['Successful', $successCount],
                ['Failed', $failureCount],
                ['Total', $eligibleTransactions->count()],
            ]
        );

        // Show payout statistics
        $stats = $this->disbursementService->getPayoutStats();
        $this->info("Current Payout Statistics:");
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Payouts Completed', $stats['total_payouts']],
                ['Pending Payouts', $stats['pending_payouts']],
                ['Failed Payouts', $stats['failed_payouts']],
                ['Total Amount Paid', '₱' . number_format($stats['total_amount_paid'], 2)],
                ['Pending Amount', '₱' . number_format($stats['pending_amount'], 2)],
            ]
        );

        return 0;
    }

    /**
     * Get eligible transactions for payout
     */
    protected function getEligibleTransactions()
    {
        $payoutDelay = config('maya.disbursement.payout_delay_hours', 24);
        $payoutTime = now()->subHours($payoutDelay);

        return MayaTransaction::where('payment_status', 'paid')
            ->where('payout_status', 'pending')
            ->where('payment_date', '<=', $payoutTime)
            ->with(['application.pet', 'shelter', 'rescuer'])
            ->get();
    }

    /**
     * Show dry run results
     */
    protected function showDryRunResults($transactions)
    {
        $this->info('DRY RUN - No actual payouts will be processed');
        $this->newLine();

        $tableData = [];
        $totalAmount = 0;

        foreach ($transactions as $transaction) {
            $provider = $transaction->shelter ?? $transaction->rescuer;
            $providerName = $provider->shelter_name ?? $provider->organization_name ?? 'Unknown';
            
            $tableData[] = [
                $transaction->transaction_id,
                $transaction->application->pet->name,
                $providerName,
                '₱' . number_format($transaction->provider_amount, 2),
                $transaction->payment_date->format('M d, Y H:i'),
            ];

            $totalAmount += $transaction->provider_amount;
        }

        $this->table(
            ['Transaction ID', 'Pet', 'Provider', 'Payout Amount', 'Payment Date'],
            $tableData
        );

        $this->info("Total payout amount: ₱" . number_format($totalAmount, 2));
        $this->info("Total transactions: " . $transactions->count());
    }
} 