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
        Schema::create('pets', function (Blueprint $table) {
            $table->id('pet_id');
            $table->unsignedBigInteger('shelter_id')->nullable();
            $table->unsignedBigInteger('rescuer_id')->nullable();
            $table->string('name');
            $table->string('species');
            $table->string('breed');
            $table->integer('age');
            $table->string('gender');
            $table->string('size');
            $table->text('medical_history')->nullable();
            $table->string('adoption_status')->nullable();
            $table->string('behavior')->nullable();
            $table->string('daily_activity')->nullable();
            $table->string('eating_habits')->nullable();
            $table->string('special_needs')->nullable();
            $table->string('compatibility')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('shelter_id')->references('shelter_id')->on('shelters')->onDelete('set null');
            $table->foreign('rescuer_id')->references('rescuer_id')->on('rescuers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
