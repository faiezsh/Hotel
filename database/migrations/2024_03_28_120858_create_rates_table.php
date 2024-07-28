<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateRatesTable extends Migration
{

    public function up()
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->tinyInteger('rate');
            $table->integer('hotelId')->unsigned();
            $table->integer('userId')->unsigned();
        });
    }

    public function down()
    {
        Schema::drop('rates');
    }
}
