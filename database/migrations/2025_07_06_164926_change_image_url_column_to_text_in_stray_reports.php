<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeImageUrlColumnToTextInStrayReports extends Migration
{
    public function up()
    {
        Schema::table('stray_reports', function (Blueprint $table) {
            $table->text('image_url')->change();
        });
    }

    public function down()
    {
        Schema::table('stray_reports', function (Blueprint $table) {
            $table->string('image_url', 255)->change();
        });
    }
}
