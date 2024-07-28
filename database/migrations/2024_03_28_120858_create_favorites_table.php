<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateFavoritesTable extends Migration
{

    public function up()
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('hotelId')->unsigned();
            $table->integer('userId')->unsigned();
        });
    }

    public function down()
    {
        Schema::drop('favorites');
    }
}
