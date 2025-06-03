<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('adopters', function (Blueprint $table) {
            $table->id('adopter_id');
            $table->unsignedBigInteger('user_id');
            $table->string('address');
            $table->string('adoption_status');
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('adopters');
    }
}; 