<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateHotelsTable extends Migration
{

    public function up()
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('roomCounts');
            $table->text('detail');
            $table->integer('locationId')->unsigned();
            $table->text('locationDetail');
            $table->integer('userId')->unsigned();
        });
    }

    public function down()
    {
        Schema::drop('hotels');
    }
}
