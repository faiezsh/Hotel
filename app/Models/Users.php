<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Users extends Authenticatable
{

    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'users';
    public $timestamps = true;
    protected $fillable = array('name', 'userName', 'password', 'roll', 'wallet');
}
