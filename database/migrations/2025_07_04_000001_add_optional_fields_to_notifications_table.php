<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->json('data')->nullable()->after('read_at');
            $table->string('action_url')->nullable()->after('data');
            $table->string('icon')->nullable()->after('action_url');
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['data', 'action_url', 'icon']);
        });
    }
}; 