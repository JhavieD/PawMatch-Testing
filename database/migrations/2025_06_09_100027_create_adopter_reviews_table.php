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
        Schema::create('adopter_reviews', function (Blueprint $table) {
            $table->id('review_id');
            $table->unsignedBigInteger('shelter_id');
            $table->unsignedBigInteger('rescuer_id')->nullable();
            $table->unsignedBigInteger('adopter_id');
            $table->unsignedTinyInteger('rating');
            $table->string('review')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // foreign keys
            $table->foreign('shelter_id')->references('shelter_id')->on('shelters')->onDelete('cascade');
            $table->foreign('rescuer_id')->references('rescuer_id')->on('rescuers')->onDelete('set null');
            $table->foreign('adopter_id')->references('adopter_id')->on('adopters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adopter_reviews');
    }
};
