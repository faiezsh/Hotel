<?php

namespace App\Http\Controllers;

use App\Models\Hotels;
use App\Models\Images;
use App\Models\Rooms;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function getNotifications()
    {
        try {
            $id = Auth::id();
            $notification = DB::table('bookings')
                ->join('rooms', 'rooms.id', '=', 'bookings.roomId')
                ->join('hotels', 'hotels.userId', '=', 'rooms.hotelId')
                ->join('users', 'hotels.userId', '=', 'users.id')
                ->orderBy('bookings.created_at', 'desc')
                ->where('bookings.userId', $id)
                ->where('bookings.endDate', '>=', date('y-m-d'))
                ->get([
                    'users.name',
                    'rooms.roomNumber',
                    'bookings.startDate',
                    'bookings.endDate'
                ]);
            $usedName = [];
            $usedStartDate = [];
            $usedEndDate = [];
            $data = [];
            $j = 0;
            $l = 0;
            for ($i = 0; $i < count($notification); $i++) {
                $k = 0;
                for ($k = 0; $k < count($usedName); $k++) {
                    if (
                        $usedName[$k] == $notification[$i]->name
                        && $usedEndDate[$k] == $notification[$i]->endDate
                        && $usedStartDate[$k] == $notification[$i]->startDate
                    ) {
                        break;
                    }
                }
                if ($k < count($usedName)) {
                    $data[$k]['roomNumber'][count($data[$k]['roomNumber'])] = $notification[$i]->roomNumber;
                } else {
                    $data[$j]['name'] = $notification[$i]->name;
                    $data[$j]['roomNumber'] = [$notification[$i]->roomNumber];
                    $data[$j]['startDate'] = $notification[$i]->startDate;
                    $data[$j]['endDate'] = $notification[$i]->endDate;
                    $j++;
                    $usedName[$l] = $notification[$i]->name;
                    $usedStartDate[$l] = $notification[$i]->startDate;
                    $usedEndDate[$l] = $notification[$i]->endDate;
                    $l++;
                }
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

    public function updateWallet(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user['roll'] != 'admin') {
                return response()->json([
                    'state' => false,
                    'data' => 'access denied'
                ], 403);
            }
            $request->validate([
                'userName' => 'required',
                'amount' => 'required'
            ]);
            $wallet = Users::where('userName', $request['userName'])->value('wallet');
            if ($wallet == null) {
                return response()->json([
                    'state' => false,
                    'data' => 'not found'
                ], 210);
            }
            Users::where('userName', $request['userName'])->update([
                'wallet' => $wallet + $request['amount']
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

    public function getWallet(Request $request)
    {
        try {
            $user = Auth::user();
            $wallet = Users::where('userName', $user['userName'])->value('wallet');
            return response()->json([
                'state' => true,
                'data' => $wallet
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'state' => false,
                'data' => $th->getMessage()
            ], 500);
        }
    }

    public function addAdmin(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user['roll'] != 'admin') {
                return response()->json([
                    'state' => false,
                    'data' => 'access denied'
                ], 403);
            }
            $request->validate([
                'userName' => 'required',
                'password' => 'required'
            ]);
            $username = Users::where('userName', $request['userName'])->first();
            if ($username != null) {
                return response()->json([
                    'state' => false,
                    'data' => 'username already exist'
                ], 210);
            }
            $newAdmin = Users::create([
                'name' => 'admin',
                'userName' => $request['userName'],
                'password' => Hash::make($request['password']),
                'roll' => 'admin'
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

    public function addHotel(Request $request)
    {
        $user = null;
        $hotel = null;
        try {
            $roll = Auth::user();
            if ($roll['roll'] != 'admin') {
                return response()->json([
                    'state' => false,
                    'data' => 'access denied'
                ], 403);
            }
            $request->validate([
                'name' => 'required',
                'password' => 'required',
                'userName' => 'required',
                'locationDetail' => 'required',
                'locationId' => 'required',
                'detail' => 'required',
                'roomCounts' => 'required',
                'roomDefault' => 'required',
                'minPrice' => 'required',
                'image' => 'required|file'
            ]);
            $username = Users::where('userName', $request['userName'])->first();
            if ($username != null) {
                return response()->json([
                    'state' => false,
                    'data' => 'username already exist'
                ], 210);
            }
            $user = Users::create([
                'name' => $request['name'],
                'password' => Hash::make($request['password']),
                'userName' => $request['userName'],
                'roll' => 'hotel'
            ]);
            $hotel = Hotels::create([
                'locationDetail' => $request['locationDetail'],
                'locationId' => $request['locationId'],
                'detail' => $request['detail'],
                'roomCounts' => $request['roomCounts'],
                'userId' => $user['id']
            ]);
            for ($i = 1; $i <= $request['roomCounts']; $i++) {
                $room = Rooms::create([
                    'hotelId' => $user['id'],
                    'type' => $request['roomDefault'],
                    'roomNumber' => $i,
                    'price' => $request['minPrice']
                ]);
            }
            $path = $request->file('image');
            $filename = time() . $path->getClientOriginalName();
            Storage::disk('public')->put($filename, File::get($path));
            $image = Images::create([
                'userId' => $user['id'],
                'image' => $filename,
                'type' => 'primary'
            ]);
            return response()->json([
                'state' => true,
                'data' => 'done'
            ], 200);
        } catch (\Throwable $th) {
            if ($user != null) {
                Users::where('id', $user['id'])->delete();
            }
            return response()->json([
                'state' => false,
                'data' => $th->getMessage()
            ], 500);
        }
    }
    public function register(Request $request)
    {
        try {
            $user = Validator::make($request->all(), [
                'userName' => 'required',
                'password' => 'required|string:min 8',
                'name' => 'required',
                'image' => 'required|file'
            ]);
            if ($user->fails()) {
                return response()->json(
                    [
                        'state' => false,
                        'massage' => 'validation error ',
                        'error' => $user->errors()
                    ],
                    401
                );
            }
            $user1 = Users::where('userName', $request->userName)->first('id');
            if ($user1 != null) {
                return response()->json([
                    'state' => false,
                    'massage' => 'the userName exist'
                ], 210);
            }
            $user = Users::create([
                'name' => $request->name,
                'userName' => $request->userName,
                'password' => Hash::make($request->password),
                'roll' => 'user'
            ]);
            $path = $request->file('image');
            $filename = time() . $path->getClientOriginalName();
            Storage::disk('public')->put($filename, File::get($path));
            $image = Images::create([
                'userId' => $user['id'],
                'image' => $filename,
                'type' => 'personal'
            ]);
            Auth::login($user);
            return response()->json([
                'state' => true,
                'massage' => 'sucssful',
                'token' => $user->createToken("Api")->plainTextToken,
                'name' => $user['name'],
                'imageId' => $image['id']
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'state' => false,
                'massage' => $th->getMessage()
            ], 500);
        }
    }
    public function login(Request $request)
    {
        try {
            $request->validate([
                'userName' => 'required',
                'password' => 'required'
            ]);
            $user = Users::where('userName', $request->userName)->first();
            if ($user == null) {
                return response()->json([
                    'state' => false,
                    'data' => 'wrong username or password'
                ], 210);
            }
            if (!Hash::check($request['password'], $user['password'])) {
                return response()->json([
                    'state' => false,
                    'data' => 'wrong username or password'
                ], 210);
            }
            Auth::login($user);
            if ($user->roll == 'hotel')
                $imageId = Images::where('userId', $user['id'])->where('type', 'primary')->value('id');
            elseif ($user->roll == 'user')
                $imageId = Images::where('userId', $user['id'])->where('type', 'personal')->value('id');
            else
                $imageId = 0;
            return response()->json([
                'state' => true,
                'massage' => 'sucssful',
                'token' => $user->createToken("Api")->plainTextToken,
                'name' => $user->name,
                'userName' => $user->userName,
                'roll' => $user->roll,
                'imageId' => $imageId
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'state' => false,
                'massage' => $th->getMessage()
            ], 500);
        }
    }
    public function logout()
    {
        try {
            auth()->user()->tokens()->delete();
            return response()->json([
                'state' => true,
                'massege' => 'logged out successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'state' => false,
                'massage' => $th->getMessage()
            ], 500);
        }
    }
}
