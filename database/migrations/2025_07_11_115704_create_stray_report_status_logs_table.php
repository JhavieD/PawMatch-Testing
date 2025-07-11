<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stray_report_status_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->unsignedBigInteger('adopter_id'); // This field stores report_id values
            $table->string('old_status', 50)->nullable(); // Fix: Make nullable for initial entries
            $table->string('new_status', 50);
            $table->unsignedBigInteger('changed_by');
            $table->timestamp('changed_at')->useCurrent();
            $table->text('notes')->nullable();
            
            // Remove foreign key constraints to avoid issues
            // We'll handle referential integrity in the application layer
            
            $table->index(['adopter_id', 'changed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stray_report_status_logs');
    }
};