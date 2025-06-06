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
        Schema::create('adoption_applications', function (Blueprint $table) {
            $table->id('application_id');
            $table->unsignedBigInteger('adopter_id');
            $table->unsignedBigInteger('pet_id');
            $table->unsignedBigInteger('shelter_id')->nullable();
            $table->unsignedBigInteger('rescuer_id')->nullable();
            $table->string('reason_for_adoption')->nullable();
            $table->string('living_environment')->nullable();
            $table->string('experience_with_pets')->nullable();
            $table->string('household_members')->nullable();
            $table->string('allergies')->nullable();
            $table->boolean('has_other_pets')->nullable();
            $table->string('other_pets_details')->nullable();
            $table->boolean('can_provide_vet_care')->nullable();
            $table->string('status')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->foreign('adopter_id')->references('adopter_id')->on('adopters')->onDelete('cascade');
            $table->foreign('pet_id')->references('pet_id')->on('pets')->onDelete('cascade');
            $table->foreign('shelter_id')->references('shelter_id')->on('shelters')->onDelete('set null');
            $table->foreign('rescuer_id')->references('rescuer_id')->on('rescuers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adoption_applications');
    }
};
