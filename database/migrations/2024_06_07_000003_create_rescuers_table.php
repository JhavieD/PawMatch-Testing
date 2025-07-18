<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rescuers', function (Blueprint $table) {
            $table->id('rescuer_id');
            $table->unsignedBigInteger('user_id');
            $table->string('organization_name');
            $table->string('location');
            $table->boolean('verified')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('rescuers');
    }
}; 