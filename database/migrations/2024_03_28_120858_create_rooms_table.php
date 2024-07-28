<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateRoomsTable extends Migration
{

    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('hotelId')->unsigned();
            $table->string('type');
            $table->integer('roomNumber');
            $table->decimal('price');
        });
    }

    public function down()
    {
        Schema::drop('rooms');
    }
}
