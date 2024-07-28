<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{

    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('image');
            $table->enum('type', array('primary', 'personal', 'preview'));
            $table->integer('userId')->unsigned();
        });
    }

    public function down()
    {
        Schema::drop('images');
    }
}
