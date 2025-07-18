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
        Schema::table('stray_report_notifications', function (Blueprint $table) {
            $table->dateTime('read_at')->nullable()->after('is_read');
            $table->dateTime('handled_at')->nullable()->after('read_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stray_report_notifications', function (Blueprint $table) {
            $table->dropColumn(['read_at', 'handled_at']);
        });
    }
};
