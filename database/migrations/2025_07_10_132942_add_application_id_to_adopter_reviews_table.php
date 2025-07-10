<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('adopter_reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('application_id')->nullable()->after('adopter_id');
            $table->foreign('application_id')
                ->references('application_id')
                ->on('adoption_applications')
                ->onDelete('cascade');

            try {
                $table->dropUnique(['adopter_id', 'shelter_id']);
            } catch (Exception $e) {
            }
            
            $table->unique('application_id');
        });
    }

    public function down(): void
    {
        Schema::table('adopter_reviews', function (Blueprint $table) {
            $table->dropForeign(['application_id']);
            $table->dropUnique(['application_id']);
            $table->dropColumn('application_id');
            $table->unique(['adopter_id', 'shelter_id']);
        });
    }
};