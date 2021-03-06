<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableGpx extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gpx', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('center_json');
            $table->longText('gpx_json');
            $table->float('distance');
            $table->float('unevenness_positive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gpx');
    }
}
