<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateLocationsTable extends Migration
{

    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('country');
            $table->string('city');
        });
    }

    public function down()
    {
        Schema::drop('locations');
    }
}
