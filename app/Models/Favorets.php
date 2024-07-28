<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorets extends Model
{

    protected $table = 'favorites';
    public $timestamps = true;
    protected $fillable = array('hotelId', 'userId');
}
