<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('stray_reports', function (Blueprint $table) {
            $table->string('animal_type')->nullable()->after('adopter_id');
            $table->string('image_url')->nullable()->after('description');
        });

        Schema::table('admin_actions', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('reason');
            $table->string('last_name')->nullable()->after('first_name');
        });
    }

    public function down()
    {
        Schema::table('stray_reports', function (Blueprint $table) {
            $table->dropColumn(['animal_type', 'image_url']);
        });

        Schema::table('admin_actions', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name']);
        });
    }
};