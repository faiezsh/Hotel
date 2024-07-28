<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Locations extends Model
{

    protected $table = 'locations';
    public $timestamps = true;
    protected $fillable = array('country', 'city');
}
