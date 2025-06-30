<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('stray_reports', function (Blueprint $table) {
            // Check if columns exist before adding them
            if (!Schema::hasColumn('stray_reports', 'animal_type')) {
                $table->string('animal_type')->nullable()->after('adopter_id');
            }
            if (!Schema::hasColumn('stray_reports', 'image_url')) {
                $table->string('image_url')->nullable()->after('description');
            }
        });

        Schema::table('admin_actions', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_actions', 'first_name')) {
                $table->string('first_name')->nullable()->after('reason');
            }
            if (!Schema::hasColumn('admin_actions', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }
        });
    }

    public function down()
    {
        Schema::table('stray_reports', function (Blueprint $table) {
            // Only drop columns if they exist
            $columnsToDrop = [];
            if (Schema::hasColumn('stray_reports', 'animal_type')) {
                $columnsToDrop[] = 'animal_type';
            }
            if (Schema::hasColumn('stray_reports', 'image_url')) {
                $columnsToDrop[] = 'image_url';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });

        Schema::table('admin_actions', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('admin_actions', 'first_name')) {
                $columnsToDrop[] = 'first_name';
            }
            if (Schema::hasColumn('admin_actions', 'last_name')) {
                $columnsToDrop[] = 'last_name';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};