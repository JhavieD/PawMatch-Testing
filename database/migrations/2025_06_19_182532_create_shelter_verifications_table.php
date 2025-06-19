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
        Schema::create('shelter_verifications', function (Blueprint $table) {
            $table->id('verification_id');
            $table->unsignedBigInteger('shelter_id');
            $table->unsignedBigInteger('submitted_by');
            $table->string('registration_doc_url');
            $table->string('facebook_link')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->foreign('shelter_id')->references('shelter_id')->on('shelters')->onDelete('cascade');
            $table->foreign('submitted_by')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('user_id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shelter_verifications');
    }
};
