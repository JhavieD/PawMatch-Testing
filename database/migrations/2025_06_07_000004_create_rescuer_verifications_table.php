<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('rescuer_verifications')) {
            Schema::create('rescuer_verifications', function (Blueprint $table) {
                $table->id('verification_id');
                $table->unsignedBigInteger('rescuer_id');
                $table->unsignedBigInteger('submitted_by');
                $table->string('document_url');
                $table->string('facebook_link')->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->unsignedBigInteger('reviewed_by')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamp('submitted_at')->useCurrent();
                $table->text('remarks')->nullable();
                $table->timestamps();

                $table->foreign('rescuer_id')->references('rescuer_id')->on('rescuers')->onDelete('cascade');
                $table->foreign('submitted_by')->references('user_id')->on('users')->onDelete('cascade');
                $table->foreign('reviewed_by')->references('user_id')->on('users')->onDelete('set null');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('rescuer_verifications');
    }
}; 