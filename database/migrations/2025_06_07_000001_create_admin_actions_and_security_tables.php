<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    // Stray reports table
    Schema::create('stray_reports', function (Blueprint $table) {
        $table->id('report_id');
        $table->unsignedBigInteger('adopter_id')->nullable();
        $table->string('location');
        $table->text('description');
        $table->string('status');
        $table->timestamp('reported_at');
        $table->timestamps();

        $table->foreign('adopter_id')->references('adopter_id')->on('adopters')->onDelete('set null');
    });

    // Admin actions table
    Schema::create('admin_actions', function (Blueprint $table) {
        $table->id('action_id');
        $table->unsignedBigInteger('admin_id');
        $table->string('action_type');
        $table->unsignedBigInteger('target_user_id')->nullable();
        $table->unsignedBigInteger('target_report_id')->nullable();
        $table->string('reason')->nullable();
        $table->timestamp('created_at')->useCurrent();

        $table->foreign('admin_id')->references('user_id')->on('users')->onDelete('cascade');
        $table->foreign('target_user_id')->references('user_id')->on('users')->onDelete('set null');
        $table->foreign('target_report_id')->references('report_id')->on('stray_reports')->onDelete('set null');
    });

    // Password resets table
    Schema::create('password_resets', function (Blueprint $table) {
        $table->id('reset_id');
        $table->unsignedBigInteger('user_id');
        $table->string('token');
        $table->timestamp('expires_at');
        $table->timestamp('used_at')->nullable();
        $table->timestamps();

        $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_actions');
        Schema::dropIfExists('stray_reports');
        Schema::dropIfExists('adopters');
        Schema::dropIfExists('password_resets');
        Schema::dropIfExists('users');
    }
}; 