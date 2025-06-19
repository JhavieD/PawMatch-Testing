<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_answers', function (Blueprint $table) {
            $table->id('answer_id');
            $table->unsignedBigInteger('application_id');
            $table->text('question');
            $table->text('answer');
            $table->timestamps();

            $table->foreign('application_id')->references('application_id')->on('adoption_applications')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_answers');
    }
}; 
