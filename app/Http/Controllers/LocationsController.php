<?php

namespace App\Http\Controllers;

use App\Models\Locations;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationsController extends Controller
{
    public function getAllLocations()
    {
        try {
            return response()->json([
                'state' => true,
                'data' => Locations::orderBy('country','asc')
                ->orderBy('city','asc')
                ->get(['id', 'country', 'city'])
            ]);
        } catch (Exception $e) {
            return response()->json([
                'state' => false,
                'data' => $e->getMessage()
            ], 500);
        }
    }
    public function addLocation(Request $request)
    {
        $user = Auth::user();
        if ($user['roll'] == 'admin') {
            try {
                $Location = $request->validate([
                    'country' => 'required|string',
                    'city' => 'required|string'
                ]);
                $l = Locations::where('country', $Location['country'])->where('city', $Location['city'])->first();
                if ($l!=null) {
                    return response()->json([
                        'state' => false,
                    ],210);
                }
                $locat = Locations::create(
                    [
                        'country' => $Location['country'],
                        'city' => $Location['city']
                    ]
                );
                return response()->json([
                    'state' => true,
                    'massage' => 'sucssfuly'
                ]);
            } catch (\Throwable $th) {
                //throw $th;
                return response()->json([
                    'state' => false,
                    'data' => $th->getMessage()
                ], 500);
            }
        }
        return response()->json([
            'state' => false,
            'data' => 'access denied'
        ], 403);
    }
}
