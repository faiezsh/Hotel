<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class favHotel extends Model
{

    protected $table = 'hotels';
    public $timestamps = true;
    protected $hidden = array('roomCounts', 'detail', 'locationId', 'created_at', 'updated_at', 'id');
    protected $fillable = array('locationDetail', 'userId', 'id');
    protected $appends = array('imageId', 'name', 'minPrice', 'rate', 'location', 'isOffer');

    public function getIsOfferAttribute()
    {
        $room = Rooms::where('hotelId', $this->userId)->pluck('id');
        $offer = Offers::whereIn('roomId', $room)->where('endOfferDate', '>=', date('y-m-d'))->value('id');
        if ($offer != null) {
            return true;
        }
        return false;
    }
    public function getLocationAttribute()
    {
        return Locations::where('id', $this->locationId)->first(['country', 'city']);
    }

    public function getNameAttribute()
    {
        $id = Hotels::where('id', $this->id)->value('userId');
        return Users::where('id', $id)->value('name');
    }

    public function getImageIdAttribute()
    {
        $id = Hotels::where('id', $this->id)->value('userId');
        return Images::where('userId', $id)->where('type', 'primary')->value('id');
    }

    public function getMinPriceAttribute()
    {
        $id = Hotels::where('id', $this->id)->value('userId');
        $prices = Rooms::where('hotelId', $id)->pluck('price');
        $price = $prices[0];
        for ($i = 1; $i < count($prices); $i++) {
            $prices[$i] < $price ? $price = $prices[$i] : $price = $price;
        }
        return $price;
    }

    public function getRateAttribute()
    {
        $id = Hotels::where('id', $this->id)->value('userId');
        $rate = Rates::where('hotelId', $id)->avg('rate');
        return round($rate, 0);
    }
}
