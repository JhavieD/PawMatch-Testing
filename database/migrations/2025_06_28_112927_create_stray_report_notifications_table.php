<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stray_report_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_id');
            $table->unsignedBigInteger('shelter_id');
            $table->timestamp('sent_at');
            $table->boolean('is_read')->default(false);
            $table->text('admin_message')->nullable();
            $table->timestamps();

            $table->foreign('report_id')->references('report_id')->on('stray_reports')->onDelete('cascade');
            $table->foreign('shelter_id')->references('shelter_id')->on('shelters')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stray_report_notifications');
    }
};