<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('maya_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('maya_transactions', 'payout_status')) {
                $table->enum('payout_status', ['pending', 'processing', 'completed', 'failed'])->default('pending')->after('payment_status');
            }
            if (!Schema::hasColumn('maya_transactions', 'payout_date')) {
                $table->timestamp('payout_date')->nullable()->after('payout_status');
            }
            if (!Schema::hasColumn('maya_transactions', 'payout_reference')) {
                $table->string('payout_reference')->nullable()->after('payout_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maya_transactions', function (Blueprint $table) {
            $table->dropColumn(['payout_status', 'payout_date', 'payout_reference']);
        });
    }
}; 