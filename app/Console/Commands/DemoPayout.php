<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Shared\MayaTransaction;
use App\Services\MayaDisbursementService;

class DemoPayout extends Command
{
    protected $signature = 'demo:payout {action=show} {--bypass-delay}';
    protected $description = 'Demonstrate payout system with optional time delay bypass';

    public function handle()
    {
        $action = $this->argument('action');
        $bypassDelay = $this->option('bypass-delay');
        
        switch ($action) {
            case 'show':
                $this->showCurrentStatus();
                break;
            case 'process':
                $this->processPayout($bypassDelay);
                break;
            case 'reset':
                $this->resetForDemo();
                break;
            case 'stats':
                $this->showStats();
                break;
            default:
                $this->error("Unknown action: {$action}");
                $this->info("Available actions: show, process, reset, stats");
        }
    }
    
    private function showCurrentStatus()
    {
        $this->info("🐾 PAWMATCH PAYOUT SYSTEM - CURRENT STATUS");
        $this->line("=" . str_repeat("=", 50));
        
        $transactions = MayaTransaction::with(['application.pet', 'shelter', 'rescuer', 'adopter.user'])
            ->orderBy('transaction_id', 'desc')
            ->get();
            
        if ($transactions->isEmpty()) {
            $this->warn("No transactions found");
            return;
        }
        
        foreach ($transactions as $transaction) {
            $this->line("\n📋 Transaction ID: {$transaction->transaction_id}");
            $this->line("🐕 Pet: {$transaction->application->pet->name} ({$transaction->application->pet->breed})");
            $this->line("🏠 Provider: {$transaction->provider_name} ({$transaction->provider_type})");
            $this->line("💰 Amount: ₱" . number_format($transaction->total_amount, 2));
            $this->line("💸 Commission: ₱" . number_format($transaction->pawmatch_commission, 2));
            $this->line("💳 Provider Payout: ₱" . number_format($transaction->provider_amount, 2));
            $this->line("📅 Payment Date: {$transaction->payment_date}");
            $this->line("✅ Payment Status: {$transaction->payment_status}");
            $this->line("🔄 Payout Status: {$transaction->payout_status}");
            
            // Check eligibility
            $isEligible = $this->checkEligibility($transaction);
            $this->line("🎯 Eligible for Payout: " . ($isEligible ? 'Yes' : 'No'));
            
            if (!$isEligible && $transaction->payout_status === 'pending') {
                $payoutDelay = config('maya.disbursement.payout_delay_hours', 24);
                $payoutTime = $transaction->payment_date->addHours($payoutDelay);
                $this->warn("⏰ Not yet eligible. Available after: {$payoutTime}");
            }
            
            $this->line("---");
        }
    }
    
    private function processPayout($bypassDelay = false)
    {
        $this->info("\n🔄 PROCESSING PAYOUT DEMONSTRATION");
        $this->line("=" . str_repeat("=", 40));
        
        if ($bypassDelay) {
            $this->warn("⚠️  TIME DELAY BYPASSED FOR DEMONSTRATION");
        }
        
        $pendingTransaction = MayaTransaction::where('payment_status', 'paid')
            ->where('payout_status', 'pending')
            ->first();
            
        if (!$pendingTransaction) {
            $this->error("❌ No pending payouts found");
            $this->info("💡 Use 'php artisan demo:payout reset' to reset transactions for demo");
            return;
        }
        
        $this->info("Processing payout for Transaction ID: {$pendingTransaction->transaction_id}");
        $this->line("Amount: ₱" . number_format($pendingTransaction->provider_amount, 2));
        $this->line("Provider: {$pendingTransaction->provider_name}");
        
        // Temporarily modify payment date if bypassing delay
        if ($bypassDelay) {
            $originalDate = $pendingTransaction->payment_date;
            $pendingTransaction->payment_date = now()->subHours(25); // Make it eligible
            $pendingTransaction->save();
            $this->info("⏰ Temporarily modified payment date for demonstration");
        }
        
        $disbursementService = app(MayaDisbursementService::class);
        $result = $disbursementService->processPayout($pendingTransaction);
        
        // Restore original date if bypassed
        if ($bypassDelay) {
            $pendingTransaction->payment_date = $originalDate;
            $pendingTransaction->save();
            $this->info("⏰ Restored original payment date");
        }
        
        if ($result) {
            $this->info("✅ Payout processed successfully!");
            $this->line("📧 Email notification sent to provider");
            $this->line("💾 Database updated with completion status");
        } else {
            $this->error("❌ Payout processing failed");
        }
    }
    
    private function resetForDemo()
    {
        $this->info("\n🔄 RESETTING TRANSACTIONS FOR DEMONSTRATION");
        $this->line("=" . str_repeat("=", 40));
        
        $this->warn("This will reset all completed/processing payouts to pending status.");
        
        if (!$this->confirm('Are you sure you want to reset all payout statuses?')) {
            $this->info("Reset cancelled");
            return;
        }
        
        $updated = MayaTransaction::whereIn('payout_status', ['completed', 'processing'])
            ->update([
                'payout_status' => 'pending',
                'payout_date' => null,
                'payout_reference' => null
            ]);
            
        $this->info("✅ Reset {$updated} transactions to pending status");
        $this->line("🎯 You can now demonstrate payout processing again");
        $this->line("💡 Use 'php artisan demo:payout process --bypass-delay' to skip time delay");
    }
    
    private function showStats()
    {
        $this->info("\n📊 PAYOUT SYSTEM STATISTICS");
        $this->line("=" . str_repeat("=", 40));
        
        $totalTransactions = MayaTransaction::count();
        $paidTransactions = MayaTransaction::where('payment_status', 'paid')->count();
        $pendingPayouts = MayaTransaction::where('payout_status', 'pending')->count();
        $processingPayouts = MayaTransaction::where('payout_status', 'processing')->count();
        $completedPayouts = MayaTransaction::where('payout_status', 'completed')->count();
        
        $totalRevenue = MayaTransaction::where('payment_status', 'paid')->sum('total_amount');
        $totalCommission = MayaTransaction::where('payment_status', 'paid')->sum('pawmatch_commission');
        $totalPayouts = MayaTransaction::where('payout_status', 'completed')->sum('provider_amount');
        $pendingAmount = MayaTransaction::where('payout_status', 'pending')->sum('provider_amount');
        
        $this->info("\n💰 FINANCIAL SUMMARY:");
        $this->line("Total Revenue: ₱" . number_format($totalRevenue, 2));
        $this->line("PawMatch Commission (20%): ₱" . number_format($totalCommission, 2));
        $this->line("Provider Payouts (80%): ₱" . number_format($totalPayouts, 2));
        $this->line("Pending Payouts: ₱" . number_format($pendingAmount, 2));
        
        $this->info("\n📈 STATUS BREAKDOWN:");
        $this->line("Total Transactions: {$totalTransactions}");
        $this->line("Paid Transactions: {$paidTransactions}");
        $this->line("Pending Payouts: {$pendingPayouts}");
        $this->line("Processing Payouts: {$processingPayouts}");
        $this->line("Completed Payouts: {$completedPayouts}");
        
        if ($totalRevenue > 0) {
            $successRate = ($completedPayouts / $paidTransactions) * 100;
            $this->info("\n✅ SUCCESS RATE:");
            $this->line("Payout Success Rate: " . number_format($successRate, 1) . "%");
        }
    }
    
    private function checkEligibility($transaction)
    {
        if ($transaction->payment_status !== 'paid') {
            return false;
        }

        if (in_array($transaction->payout_status, ['processing', 'completed'])) {
            return false;
        }

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
} 