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
        // Add payment fields to shelters table
        Schema::table('shelters', function (Blueprint $table) {
            if (!Schema::hasColumn('shelters', 'adoption_fee')) {
                $table->decimal('adoption_fee', 10, 2)->default(0.00)->after('avg_adopter_rating');
            }
            if (!Schema::hasColumn('shelters', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('adoption_fee');
            }
            if (!Schema::hasColumn('shelters', 'bank_account_number')) {
                $table->string('bank_account_number')->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('shelters', 'bank_account_name')) {
                $table->string('bank_account_name')->nullable()->after('bank_account_number');
            }
        });

        // Add payment fields to rescuers table
        Schema::table('rescuers', function (Blueprint $table) {
            if (!Schema::hasColumn('rescuers', 'adoption_fee')) {
                $table->decimal('adoption_fee', 10, 2)->default(0.00)->after('verified');
            }
            if (!Schema::hasColumn('rescuers', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('adoption_fee');
            }
            if (!Schema::hasColumn('rescuers', 'bank_account_number')) {
                $table->string('bank_account_number')->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('rescuers', 'bank_account_name')) {
                $table->string('bank_account_name')->nullable()->after('bank_account_number');
            }
        });

        // Add payment tracking to adoption_applications table
        Schema::table('adoption_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('adoption_applications', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending')->after('submitted_at');
            }
            if (!Schema::hasColumn('adoption_applications', 'payment_amount')) {
                $table->decimal('payment_amount', 10, 2)->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('adoption_applications', 'payment_date')) {
                $table->timestamp('payment_date')->nullable()->after('payment_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove payment fields from shelters table
        Schema::table('shelters', function (Blueprint $table) {
            $columns = ['adoption_fee', 'bank_name', 'bank_account_number', 'bank_account_name'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('shelters', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // Remove payment fields from rescuers table
        Schema::table('rescuers', function (Blueprint $table) {
            $columns = ['adoption_fee', 'bank_name', 'bank_account_number', 'bank_account_name'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('rescuers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // Remove payment tracking from adoption_applications table
        Schema::table('adoption_applications', function (Blueprint $table) {
            $columns = ['payment_status', 'payment_amount', 'payment_date'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('adoption_applications', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}; 