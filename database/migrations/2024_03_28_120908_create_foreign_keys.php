<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateForeignKeys extends Migration
{

    public function up()
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->foreign('locationId')->references('id')->on('locations')
                ->onDelete('cascade')
                ->onUpdate('restrict');
        });
        Schema::table('hotels', function (Blueprint $table) {
            $table->foreign('userId')->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('restrict');
        });
        Schema::table('rooms', function (Blueprint $table) {
            $table->foreign('hotelId')->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('restrict');
        });
        Schema::table('images', function (Blueprint $table) {
            $table->foreign('userId')->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('restrict');
        });
        Schema::table('favorites', function (Blueprint $table) {
            $table->foreign('hotelId')->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('restrict');
        });
        Schema::table('favorites', function (Blueprint $table) {
            $table->foreign('userId')->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('restrict');
        });
        Schema::table('rates', function (Blueprint $table) {
            $table->foreign('hotelId')->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('restrict');
        });
        Schema::table('rates', function (Blueprint $table) {
            $table->foreign('userId')->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('restrict');
        });
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('roomId')->references('id')->on('rooms')
                ->onDelete('cascade')
                ->onUpdate('restrict');
        });
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('userId')->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('restrict');
        });
        Schema::table('offers', function (Blueprint $table) {
            $table->foreign('roomId')->references('id')->on('rooms')
                ->onDelete('cascade')
                ->onUpdate('restrict');
        });
    }

    public function down()
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropForeign('hotels_locationId_foreign');
        });
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropForeign('hotels_userId_foreign');
        });
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign('rooms_hotelId_foreign');
        });
        Schema::table('images', function (Blueprint $table) {
            $table->dropForeign('images_userId_foreign');
        });
        Schema::table('favorites', function (Blueprint $table) {
            $table->dropForeign('favorites_hotelId_foreign');
        });
        Schema::table('favorites', function (Blueprint $table) {
            $table->dropForeign('favorites_userId_foreign');
        });
        Schema::table('rates', function (Blueprint $table) {
            $table->dropForeign('rates_hotelId_foreign');
        });
        Schema::table('rates', function (Blueprint $table) {
            $table->dropForeign('rates_userId_foreign');
        });
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign('bookings_roomId_foreign');
        });
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign('bookings_userId_foreign');
        });
        Schema::table('offers', function (Blueprint $table) {
            $table->dropForeign('offers_roomId_foreign');
        });
    }
}
