<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offers extends Model
{

    protected $table = 'offers';
    public $timestamps = true;
    protected $fillable = array('newPrice', 'startOfferDate', 'endOfferDate', 'roomId');
}
