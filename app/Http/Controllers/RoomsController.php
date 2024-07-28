<?php

namespace App\Http\Controllers;

use App\Models\Bookings;
use App\Models\Rooms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomsController extends Controller
{
    public function roomDetail(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user['roll' != 'hotel']) {
                return response()->json([
                    'state' => false,
                    'data' => 'access denied'
                ], 403);
            }
            $request->validate([
                'roomId' => 'required'
            ]);
            $roomDetail = Rooms::where('id', $request['roomId'])->where('hotelId', $user['id'])->first(['roomNumber', 'type', 'price']);
            $bookings = Bookings::where('roomId', $request['roomId'])->where('endDate', '>=', date('y-m-d'))->get(['startDate', 'endDate']);
            return response()->json([
                'state' => true,
                'roomDetail' => $roomDetail,
                'bookings' => $bookings
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'massage' => $th->getMessage()
            ], 500);
        }
    }

    public function getHotelRoomsByType(Request $request)
    {
        try {
            $request->validate([
                'hotelId' => 'required',
                'type' => 'required'
            ]);
            $roomsCount = Rooms::where('hotelId', $request['hotelId'])->where('type', $request['type'])->count();
            $rooms = Rooms::where('hotelId', $request['hotelId'])->where('type', $request['type'])->get(['id', 'roomNumber', 'price']);;
            $data = [
                'roomsCount' => $roomsCount,
                'rooms' => $rooms
            ];
            return response()->json([
                'state' => true,
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'massage' => $th->getMessage()
            ], 500);
        }
    }

    public function getHotelRoomTypes(Request $request)
    {
        try {
            $request->validate([
                'hotelId' => 'required'
            ]);
            $types = Rooms::where('hotelId', $request['hotelId'])->distinct()->pluck('type');
            return response()->json([
                'state' => true,
                'data' => $types
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'massage' => $th->getMessage()
            ], 500);
        }
    }

    public function updateRoom(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required',
                'type' => 'required',
                'price' => 'required|integer'
            ]);
            Rooms::where('id', $request->id)->update([
                'type' => $request->type,
                'price' => $request->price
            ]);
            return response()->json([
                'state' => true,
                'massage' => 'sucssfuly'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'state' => false,
                'massage' => $th->getMessage()
            ], 500);
        }
    }
}
