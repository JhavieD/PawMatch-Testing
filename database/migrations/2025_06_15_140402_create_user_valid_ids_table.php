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
        Schema::create('user_valid_ids', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('image_url')->nullable();
            $table->string('file_type')->nullable();
            $table->timestamp('uploaded_at')->useCurrent();
            $table->boolean('approved')->default(false);
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();

            // Foreign key to users table
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('user_id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_valid_ids');
    }
};
