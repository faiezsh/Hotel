<?php

namespace App\Http\Controllers;

use App\Models\allHotel;
use App\Models\Bookings;
use App\Models\favHotel;
use App\Models\Favorets;
use App\Models\Hotels;
use App\Models\Locations;
use App\Models\Offers;
use App\Models\Rates;
use App\Models\Rooms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HotelsController extends Controller
{

    public function updateDetail(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user['roll'] != 'hotel') {
                return response()->json([
                    'state' => false,
                    'data' => 'access denied'
                ], 403);
            }
            $request->validate([
                'detail' => 'required'
            ]);
            Hotels::where('userId', $user['id'])->update([
                'detail' => $request['detail'],
            ]);
            return response()->json([
                'state' => true
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'state' => false,
                'data' => $th->getMessage()
            ], 500);
        }
    }

    public function updateLocationDetail(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user['roll'] != 'hotel') {
                return response()->json([
                    'state' => false,
                    'data' => 'access denied'
                ], 403);
            }
            $request->validate([
                'locationDetail' => 'required'
            ]);
            Hotels::where('userId', $user['id'])->update([
                'locationDetail' => $request['locationDetail']
            ]);
            return response()->json([
                'state' => true
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'state' => false,
                'data' => $th->getMessage()
            ], 500);
        }
    }

    public function hotelRooms(Request $request)
    {
        try {
            $hotel = Auth::user();
            if ($hotel['roll'] != 'hotel') {
                return response()->json([
                    'state' => false,
                    'data' => 'access denied'
                ], 403);
            }
            $request->validate([
                'startDate' => 'required',
                'endDate' => 'required'
            ]);
            $roomCount = Hotels::where('userId', $hotel['id'])->value('roomCounts');
            $rooms = Rooms::where('hotelId', $hotel['id'])->get(['id', 'roomNumber']);
            for ($i = 0; $i < count($rooms); $i++) {

                $rooms[$i]['isBooked'] = Bookings::where('roomId', $rooms[$i]['id'])
                    ->where('endDate', '>=', $request['startDate'])
                    ->where('startDate', '<=', $request['endDate'])
                    ->first('id') != null;
            }
            return response()->json([
                'state' => true,
                'roomCount' => $roomCount,
                'rooms' => $rooms
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'state' => false,
                'data' => $th->getMessage()
            ], 500);
        }
    }

    public function getHotelDetail()
    {
        try {
            $user = Auth::user();
            if ($user['roll'] == 'hotel') {
                $hotel = Hotels::where('userId', $user['id'])->first();
                $hotel['types'] = Rooms::where('hotelId', $user['id'])->distinct()->pluck('type');
                return response()->json([
                    'state' => true,
                    'data' => $hotel
                ], 200);
            }
            return response()->json([
                'state' => false,
                'data' => 'access denied'
            ], 403);
        } catch (\Throwable $th) {
            return response()->json([
                'state' => false,
                'data' => $th->getMessage()
            ], 500);
        }
    }

    public function getHotel(Request $request)
    {
        try {
            // return $request['id'];
            $user = Auth::user();
            $request->validate([
                'id' => 'required'
            ]);
            $hotel = Hotels::where('userId', $request['id'])->first();
            $hotel['isFav'] = Favorets::where('hotelId', $hotel['userId'])->where('userId', $user['id'])->first() != null;
            $roomIds = Rooms::where('hotelId', $request['id'])->pluck('id');
            $hotel['offer'] = Offers::where('endOfferDate', '>=', date('y-m-d'))
                ->whereIn('roomId', $roomIds)->orderBy('startOfferDate', 'desc')
                ->first(['startOfferDate', 'endOfferDate']);
            $hotel['userRate'] = Rates::where('userId', $user['id'])->where('hotelId', $request['id'])->value('rate');
            return response()->json([
                'state' => true,
                'data' => $hotel
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'state' => false,
                'data' => $th->getMessage()
            ], 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $id = Auth::id();
            $byCountry = [];
            $byCity = [];
            $byName = [];
            $byPrice = [];
            $hotelsId = [];
            if ($request['city'] != null) {
                $lId = Locations::where('city', $request['city'])->pluck('id');
                $byCity = Hotels::whereIn('locationId', $lId)->pluck('userId');
                $hotelsId = $byCity->toArray();
            }
            if ($request['country'] != null) {
                $lId = Locations::where('country', $request['country'])->pluck('id');
                $byCountry = Hotels::whereIn('locationId', $lId)->pluck('userId');
                $hotelsId = $byCountry->toArray();
            }
            if ($request['name'] != null) {
                $byName = Hotels::whereIn('name', $request['name'])->where('type', 'hotel')->pluck('userId');
                $hotelsId = $byName->toArray();
            }
            if ($request['price'] != null) {
                $byPrice = Rooms::where('price', '<=', $request['price'])->distinct()->pluck('hotelId');
                $hotelsId = $byPrice->toArray();
            }
            if ($request['city'] != null) {
                $hotelsId = array_intersect($byCity->toArray(), $hotelsId);
            }
            if ($request['country'] != null) {
                $hotelsId = array_intersect($byCountry->toArray(), $hotelsId);
            }
            if ($request['name'] != null) {
                $hotelsId = array_intersect($byName->toArray(), $hotelsId);
            }
            if ($request['price'] != null) {
                $hotelsId = array_intersect($byPrice->toArray(), $hotelsId);
            }
            $hotels = allHotel::whereIn('userId', $hotelsId)->get();
            for ($i = 0; $i < count($hotels); $i++) {
                $hotels[$i]['isFav'] = Favorets::where('hotelId', $hotels[$i]['userId'])->where('userId', $id)->first() != null;
            }
            return response()->json([
                'state' => true,
                'data' => $hotels
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'state' => false,
                'data' => $th->getMessage()
            ], 500);
        }
    }

    public function getAllHotels()
    {
        try {
            $id = Auth::id();
            $hotels = allHotel::limit(20)->get();
            for ($i = 0; $i < count($hotels); $i++) {
                $hotels[$i]['isFav'] = Favorets::where('hotelId', $hotels[$i]['userId'])->where('userId', $id)->first() != null ? true : false;
            }
            return response()->json([
                'state' => true,
                'data' => $hotels
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'state' => false,
                'data' => $th->getMessage()
            ], 500);
        }
    }

    public function favHotel()
    {
        try {
            $id = Auth::id();
            $ids = DB::table('hotels')->join('favorites', 'hotels.userId', '=', 'favorites.hotelId')
                ->where('favorites.userId', $id)
                ->orderBy('favorites.created_at', 'desc')
                ->pluck('hotels.userId');
            $data = [];
            for ($i = 0; $i < count($ids); $i++) {
                $data[$i] = favHotel::where('userId', $ids[$i])->first();
            }
            return response()->json([
                'state' => true,
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'state' => false,
                'data' => $th->getMessage()
            ], 500);
        }
    }
}
