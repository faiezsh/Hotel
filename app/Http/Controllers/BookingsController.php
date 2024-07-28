<?php

namespace App\Http\Controllers;

use App\Models\Bookings;
use App\Models\Hotels;
use App\Models\Offers;
use App\Models\Rooms;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingsController extends Controller
{

    public function getBookingPrice(Request $request)
    {
        try {
            $request->validate([
                'startDate' => 'required',
                'endDate' => 'required',
                'roomId' => 'required'
            ]);
            $prices = [];
            $offers = [];
            for ($i = 0; $i < count($request['roomId']); $i++) {
                $offers[$i] = Offers::where('roomId', $request['roomId'][$i])
                    ->where('endOfferDate', '>=', $request['startDate'])
                    ->where('startOfferDate', '<=', $request['endDate'])
                    ->first(['startOfferDate', 'endOfferDate', 'newPrice']);
            }
            for ($i = 0; $i < count($request['roomId']); $i++) {
                if ($offers[$i] != null) {
                    $start = new Carbon(max($request['startDate'], $offers[$i]['startOfferDate']));
                    $end = new Carbon(min($request['endDate'], $offers[$i]['endOfferDate']));
                    $days = $start->diffInDays($end) + 1;
                    $offerPrice = $days * $offers[$i]['newPrice'];
                    $start = new Carbon($request['startDate']);
                    $end = new Carbon($request['endDate']);
                    $allDays = $start->diffInDays($end) + 1;
                    $price = ($allDays - $days) * Rooms::where('id', $request['roomId'][$i])->value('price');
                    $prices[$i] = $price + $offerPrice;
                } else {
                    $start = new Carbon($request['startDate']);
                    $end = new Carbon($request['endDate']);
                    $allDays = $start->diffInDays($end) + 1;
                    $price = ($allDays) * Rooms::where('id', $request['roomId'][$i])->value('price');
                    $prices[$i] = $price;
                }
            }
            $total = 0;
            for ($i = 0; $i < count($prices); $i++) {
                $total += $prices[$i];
            }
            return response()->json([
                'state' => true,
                'data' => $total
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'state' => false,
                'data' => $th->getMessage()
            ], 500);
        }
    }

    public function addBookings(Request $request)
    {
        try {
            //code...
            $booking = $request->validate([
                'startDate' => 'required|date',
                'endDate' => 'required|date',
                'roomId' => 'required',
                'total' => 'required'
            ]);
            $id = Auth::id();
            $bookings = DB::table('bookings')->join('rooms', 'rooms.id', '=', 'bookings.roomId')->whereIn('bookings.roomId', $request['roomId'])
                ->where('bookings.endDate', '>=', $request['startDate'])
                ->where('bookings.startDate', '<=', $request['endDate'])
                ->pluck('rooms.roomNumber');
            if (count($bookings) != 0) {
                return response()->json([
                    'state' => false,
                    'massege' => 'the rooms is booking',
                    'data' => $bookings
                ], 210);
            }
            $wallet = Users::where('id', $id)->value('wallet');
            if (($wallet - $booking['total']) >= 0) {
                $hotelId = Rooms::whereIn('id', $booking['roomId'])->value('hotelId');
                $userId = Hotels::where('id', $hotelId)->value('userId');
                $hotelWallet = Users::where('id', $userId)->value('wallet');
                Users::where('id', $userId)->update([
                    'wallet' => ($hotelWallet + $booking['total'])
                ]);
                Users::where('id', $id)->update([
                    'wallet' => ($wallet - $booking['total'])
                ]);
                for ($i = 0; $i < count($booking['roomId']); $i++) {
                    $data = Bookings::create([
                        'startDate' => $booking['startDate'],
                        'endDate' => $booking['endDate'],
                        'roomId' => $booking['roomId'][$i],
                        'userId' => $id
                    ]);
                }

                return response()->json([
                    'state' => true,
                    'data' => 'successfuly'
                ], 200);
            } else {
                return response()->json([
                    'state' => false,
                    'data' => 'charge your wallet'
                ], 250);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'state' => false,
                'data' => $th->getMessage()
            ], 500);
        }
    }

    public function showBooking()
    {
        $id = Auth::id();
        try {
            $booking = Bookings::where('userId', $id)->get();
            $room = Rooms::whereIn('id', $booking['roomId'])->get();
            $hotel = Hotels::whereIn('id', $room['hotelId'])->get();
            $data[1]["booking"] = $booking;
            $data[1]["room"] = $room;
            $data[1]["hotel"] = $hotel;
            $data = json_encode($data);
            return response()->json([
                'state' => true,
                'data' => $data
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'state' => false,
                'data' => $th->getMessage()
            ], 500);
        }
    }
}
