<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateBookingsTable extends Migration
{

    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->date('startDate');
            $table->date('endDate');
            $table->integer('roomId')->unsigned();
            $table->integer('userId')->unsigned();
        });
    }

    public function down()
    {
        Schema::drop('bookings');
    }
}
