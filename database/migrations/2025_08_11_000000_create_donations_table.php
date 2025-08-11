<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id('donation_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('donor_name')->nullable();
            $table->string('donor_email')->nullable();
            $table->decimal('amount', 10, 2);
            $table->text('message')->nullable();
            $table->string('maya_payment_id')->nullable();
            $table->string('payment_status')->default('pending');
            $table->timestamp('payment_date')->nullable();
            $table->json('maya_response')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('set null');
            $table->index(['payment_status', 'payment_date']);
            $table->index('maya_payment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
}; 