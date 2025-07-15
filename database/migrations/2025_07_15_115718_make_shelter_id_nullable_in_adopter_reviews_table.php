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
        Schema::table('adopter_reviews', function (Blueprint $table) {
            // First drop the foreign key constraint
            $table->dropForeign(['shelter_id']);
            
            // Make shelter_id nullable
            $table->unsignedBigInteger('shelter_id')->nullable()->change();
            
            // Re-add the foreign key constraint
            $table->foreign('shelter_id')->references('shelter_id')->on('shelters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('adopter_reviews', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['shelter_id']);
            
            // Make shelter_id not nullable again
            $table->unsignedBigInteger('shelter_id')->nullable(false)->change();
            
            // Re-add foreign key
            $table->foreign('shelter_id')->references('shelter_id')->on('shelters')->onDelete('cascade');
        });
    }
};
