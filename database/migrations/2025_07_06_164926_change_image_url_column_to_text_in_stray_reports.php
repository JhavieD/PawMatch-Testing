<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeImageUrlColumnToTextInStrayReports extends Migration
{
    public function up()
    {
        Schema::table('stray_reports', function (Blueprint $table) {
            // Add a new text column
            $table->text('image_url_new')->nullable();
        });

        // Copy data from old column to new column
        DB::statement('UPDATE stray_reports SET image_url_new = image_url');

        // Drop the old column
        Schema::table('stray_reports', function (Blueprint $table) {
            $table->dropColumn('image_url');
        });

        // Rename the new column to the original name
        Schema::table('stray_reports', function (Blueprint $table) {
            $table->renameColumn('image_url_new', 'image_url');
        });
    }

    public function down()
    {
        Schema::table('stray_reports', function (Blueprint $table) {
            // Add a new varchar column
            $table->string('image_url_old', 255)->nullable();
        });

        // Copy data back to varchar column
        DB::statement('UPDATE stray_reports SET image_url_old = image_url');

        // Drop the text column
        Schema::table('stray_reports', function (Blueprint $table) {
            $table->dropColumn('image_url');
        });

        // Rename the varchar column back
        Schema::table('stray_reports', function (Blueprint $table) {
            $table->renameColumn('image_url_old', 'image_url');
        });
    }
}
