<?php

namespace App\Http\Controllers;

use App\Models\Favorets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoretsController extends Controller
{
    public function addFav(Request $request)
    {
        try {
            $id = Auth::id();
            $request->validate([
                'hotelId' => 'required'
            ]);
            Favorets::create([
                'userId' => $id,
                'hotelId' => $request['hotelId']
            ]);
            return response()->json([
                'state' => true,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'state' => false,
                'data' => $th->getMessage()
            ], 500);
        }
    }
    public function deleteFav(Request $request)
    {
        try {
            $id = Auth::id();
            $request->validate([
                'hotelId' => 'required'
            ]);
            Favorets::where('userId', $id)->where('hotelId', $request['hotelId'])->delete();
            return response()->json([
                'state' => true,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'state' => false,
                'data' => $th->getMessage()
            ], 500);
        }
    }
}
