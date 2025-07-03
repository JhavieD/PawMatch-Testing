<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('stray_reports', function (Blueprint $table) {
            $table->boolean('is_flagged')->default(false)->after('status');
            $table->text('flag_reason')->nullable()->after('is_flagged');
            $table->boolean('is_duplicate')->default(false)->after('flag_reason');
            $table->string('flagged_by')->nullable()->after('is_duplicate');
            $table->timestamp('flagged_at')->nullable()->after('flagged_by');
        });
    }

    public function down()
    {
        Schema::table('stray_reports', function (Blueprint $table) {
            $table->dropColumn(['is_flagged', 'flag_reason', 'is_duplicate', 'flagged_by', 'flagged_at']);
        });
    }
};