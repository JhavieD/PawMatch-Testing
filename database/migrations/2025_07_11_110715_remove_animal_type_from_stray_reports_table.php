<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stray_reports', function (Blueprint $table) {
            if (Schema::hasColumn('stray_reports', 'animal_type')) {
                $table->dropColumn('animal_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stray_reports', function (Blueprint $table) {
            $table->string('animal_type')->nullable()->after('adopter_id');
        });
    }
};