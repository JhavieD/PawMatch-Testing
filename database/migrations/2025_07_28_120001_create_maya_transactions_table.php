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
        Schema::create('maya_transactions', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->unsignedBigInteger('application_id');
            $table->unsignedBigInteger('adopter_id');
            $table->unsignedBigInteger('shelter_id')->nullable();
            $table->unsignedBigInteger('rescuer_id')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->decimal('pawmatch_commission', 10, 2); // 20% of total_amount
            $table->decimal('provider_amount', 10, 2); // 80% of total_amount
            $table->string('maya_payment_id')->nullable(); // Maya's payment reference
            $table->string('payment_status')->default('pending'); // pending, completed, failed, refunded
            $table->string('payment_method')->nullable(); // credit_card, maya_wallet, qr_code
            $table->timestamp('payment_date')->nullable();
            $table->text('maya_response')->nullable(); // Store Maya API response
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('application_id')->references('application_id')->on('adoption_applications')->onDelete('cascade');
            $table->foreign('adopter_id')->references('adopter_id')->on('adopters')->onDelete('cascade');
            $table->foreign('shelter_id')->references('shelter_id')->on('shelters')->onDelete('set null');
            $table->foreign('rescuer_id')->references('rescuer_id')->on('rescuers')->onDelete('set null');
            
            $table->index(['payment_status', 'payment_date']);
            $table->index('maya_payment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maya_transactions');
    }
}; 