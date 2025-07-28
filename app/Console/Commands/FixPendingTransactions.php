<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Shared\MayaTransaction;
use App\Models\Shared\AdoptionApplication;

class FixPendingTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:fix-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix pending transactions that should be marked as paid';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for pending transactions that need to be updated...');

        // Find transactions where the application is marked as paid but transaction is still pending
        $transactions = MayaTransaction::where('payment_status', 'pending')
            ->whereHas('application', function($query) {
                $query->where('payment_status', 'paid');
            })
            ->get();

        if ($transactions->isEmpty()) {
            $this->info('No pending transactions found that need updating.');
            return;
        }

        $this->info("Found {$transactions->count()} pending transactions to update.");

        foreach ($transactions as $transaction) {
            $transaction->payment_status = 'paid';
            $transaction->payment_date = $transaction->application->payment_date ?? now();
            $transaction->save();

            $this->line("Updated transaction ID {$transaction->transaction_id} for application {$transaction->application_id}");
        }

        $this->info('All pending transactions have been updated to paid status.');
    }
} 