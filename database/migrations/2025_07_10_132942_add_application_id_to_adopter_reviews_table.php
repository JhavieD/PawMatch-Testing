<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add the column only if it doesn't exist
        if (!Schema::hasColumn('adopter_reviews', 'application_id')) {
            Schema::table('adopter_reviews', function (Blueprint $table) {
                $table->unsignedBigInteger('application_id')->nullable()->after('adopter_id');
            });
        }
        // Always try to add the foreign key, but catch errors if it already exists
        try {
            Schema::table('adopter_reviews', function (Blueprint $table) {
                $table->foreign('application_id')
                    ->references('application_id')
                    ->on('adoption_applications')
                    ->onDelete('cascade');
            });
        } catch (\Throwable $e) {
            // Ignore if the foreign key already exists
        }
    }

    public function down(): void
    {
        // Try to drop the foreign key, ignore errors if it doesn't exist
        try {
            Schema::table('adopter_reviews', function (Blueprint $table) {
                $table->dropForeign(['application_id']);
            });
        } catch (\Throwable $e) {
            // Ignore if the foreign key doesn't exist
        }
        // Drop the column if it exists
        if (Schema::hasColumn('adopter_reviews', 'application_id')) {
            Schema::table('adopter_reviews', function (Blueprint $table) {
                $table->dropColumn('application_id');
            });
        }
    }
};