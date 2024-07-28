<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateOffersTable extends Migration
{

    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->decimal('newPrice');
            $table->date('startOfferDate');
            $table->date('endOfferDate');
            $table->integer('roomId')->unsigned();
        });
    }

    public function down()
    {
        Schema::drop('offers');
    }
}
