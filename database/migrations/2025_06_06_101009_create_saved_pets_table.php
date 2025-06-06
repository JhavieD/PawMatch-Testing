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
        Schema::create('saved_pets', function (Blueprint $table) {
            $table->unsignedBigInteger('adopter_id');
            $table->unsignedBigInteger('pet_id');
            $table->timestamp('saved_at')->nullable();
            $table->timestamps();

            $table->primary(['adopter_id', 'pet_id']);
            $table->foreign('adopter_id')->references('adopter_id')->on('adopters')->onDelete('cascade');
            $table->foreign('pet_id')->references('pet_id')->on('pets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_pets');
    }
};
