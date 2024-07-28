<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookings extends Model
{

    protected $table = 'bookings';
    public $timestamps = true;
    protected $fillable = array('startDate', 'endDate', 'roomId', 'userId');
}
