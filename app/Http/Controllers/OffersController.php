<?php

namespace App\Http\Controllers;

use App\Models\Offers;
use App\Models\Rooms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OffersController extends Controller
{

    public function myOffers()
    {
        try {
            $id = Auth::id();
            $data = DB::table('rooms')
                ->join('offers', 'offers.roomId', '=', 'rooms.id')
                ->where('rooms.hotelId', $id)
                ->where('offers.endOfferDate', '>=', date('y-m-d'))
                ->orderBy('offers.startOfferDate')
                ->get([
                    'offers.id',
                    'offers.newPrice',
                    'offers.startOfferDate',
                    'offers.endOfferDate',
                    'rooms.type',
                    'rooms.price',
                    'rooms.roomNumber'
                ]);
            for ($i = 0; $i < count($data); $i++) {
                $data[$i]->discount = (($data[$i]->price - $data[$i]->newPrice) / $data[$i]->price) * 100.0;
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

    public function deleteOffer(Request $request)
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
                'offerId' => 'required'
            ]);
            $roomId = Offers::where('id', $request['offerId'])->value('roomId');
            $hotelId = Rooms::where('id', $roomId)->value('hotelId');
            if ($user['id'] != $hotelId) {
                return response()->json([
                    'state' => false,
                    'data' => 'access denied'
                ], 403);
            }
            Offers::where('endOfferDate', '<', date('y-m-d'))->delete();
            Offers::where('id', $request['offerId'])->delete();
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
    public function addOffer(Request $request)
    {
        $user = Auth::user();
        if ($user['roll'] != 'hotel') {
            return response()->json([
                'state' => false,
                'data' => 'access denied'
            ], 403);
        }
        try {
            $request->validate([
                'roomNumber' => 'required',
                'startDate' => 'required',
                'endDate' => 'required',
                'discount' => 'required'
            ]);
            $rooms = Rooms::where('hotelId', $user['id'])
                ->whereIn('roomNumber', $request['roomNumber'])
                ->pluck('id');
            $offers = Offers::whereIn('roomId', $rooms)
                ->where('endOfferDate', '>=', $request['startDate'])
                ->where('startOfferDate', '<=', $request['endDate'])
                ->pluck('roomId');
            if (count($offers) != 0) {
                $roomNumbers = Rooms::whereIn('id', $offers)->pluck('roomNumber');
                return response()->json([
                    'state' => false,
                    'data' => $roomNumbers
                ], 210);
            }
            for ($i = 0; $i < count($request['roomNumber']); $i++) {
                $room = Rooms::where('hotelId', $user['id'])
                    ->where('roomNumber', $request['roomNumber'][$i])
                    ->first(['id', 'price']);
                $discount = 1 - ($request['discount'] / 100);
                Offers::create([
                    'newPrice' => ($room['price'] * $discount),
                    'startOfferDate' => $request['startDate'],
                    'endOfferDate' => $request['endDate'],
                    'roomId' => $room['id']
                ]);
            }

            return response()->json([
                'state' => true,
                'data' => 'sucssfly'
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'state' => false,
                'data' => $th->getMessage()
            ], 500);
        }
    }

    public static function check($id)
    {
        $date = date('y-m-d');
        $offer = Offers::where('roomId', $id)->value('endOfferDate');
        if ($offer != null && $offer >= $date) {
            return true;
        }
        return false;
    }
}
