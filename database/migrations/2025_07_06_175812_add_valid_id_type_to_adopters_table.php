<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddValidIdTypeToAdoptersTable extends Migration
{
    public function up()
    {
        Schema::table('adopters', function (Blueprint $table) {
            $table->string('valid_id_type')->nullable()->after('valid_id_url');
        });
    }

    public function down()
    {
        Schema::table('adopters', function (Blueprint $table) {
            $table->dropColumn('valid_id_type');
        });
    }
}
