<?php

use App\Models\Users;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;


class CreateUsersTable extends Migration
{

    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
            $table->string('userName')->unique();
            $table->string('password');
            $table->enum('roll', array('admin', 'user', 'hotel'));
            $table->decimal('wallet', 12, 2)->default('0');
        });
        Users::create([
            'name' => 'admin',
            'userName' => 'admin',
            'password' => Hash::make('123456789'),
            'roll' => 'admin',
        ]);
    }

    public function down()
    {
        Schema::drop('users');
    }
}
