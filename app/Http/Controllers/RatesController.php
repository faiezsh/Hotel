<?php

namespace App\Http\Controllers;

use App\Models\Rates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatesController extends Controller
{
    public function rate(Request $request)
    {
        try {
            $id = Auth::id();
            $request->validate([
                'hotelId' => 'required',
                'rate' => 'required'
            ]);
            $rate = Rates::where('userId', $id)->where('hotelId', $request['hotelId'])->first();
            if ($rate == null)
                Rates::create([
                    'userId' => $id,
                    'hotelId' => $request['hotelId'],
                    'rate' => $request['rate']
                ]);
            else
                Rates::where('userId', $id)->where('hotelId', $request['hotelId'])->update([
                    'rate' => $request['rate']
                ]);
            return response()->json([
                'state' => true
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'state' => false,
                'data' => $th->getMessage()
            ]);
        }
    }
}
