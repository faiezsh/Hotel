<?php

use App\Http\Controllers\BookingsController;
use App\Http\Controllers\FavoretsController;
use App\Http\Controllers\HotelsController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\LocationsController;
use App\Http\Controllers\OffersController;
use App\Http\Controllers\RatesController;
use App\Http\Controllers\RoomsController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/**user */
Route::post("/login", [UsersController::class, 'login']);
Route::post("/register", [UsersController::class, 'register']);
Route::post("/addHotel", [UsersController::class, 'addHotel'])->middleware('auth:sanctum');
Route::post("/addAdmin", [UsersController::class, 'addAdmin'])->middleware('auth:sanctum');
Route::post("/logout", [UsersController::class, 'logout'])->middleware('auth:sanctum');
Route::get("/getWallet", [UsersController::class, 'getWallet'])->middleware('auth:sanctum');
Route::get("/getNotifications", [UsersController::class, 'getNotifications'])->middleware('auth:sanctum');
Route::post("/updateWallet", [UsersController::class, 'updateWallet'])->middleware('auth:sanctum');
/**hotel */
Route::get("/getAllHotels", [HotelsController::class, 'getAllHotels'])->middleware('auth:sanctum');
Route::get("/favHotel", [HotelsController::class, 'favHotel'])->middleware('auth:sanctum');
Route::get("/getHotel", [HotelsController::class, 'getHotel'])->middleware('auth:sanctum');
Route::get("/getHotelDetail", [HotelsController::class, 'getHotelDetail'])->middleware('auth:sanctum');
Route::get("/hotelRooms", [HotelsController::class, 'hotelRooms'])->middleware('auth:sanctum');
Route::post("/updateLocationDetail", [HotelsController::class, 'updateLocationDetail'])->middleware('auth:sanctum');
Route::post("/updateDetail", [HotelsController::class, 'updateDetail'])->middleware('auth:sanctum');
/**room */
Route::get("/getHotelRoomTypes", [RoomsController::class, 'getHotelRoomTypes']);
Route::get("/getHotelRoomsByType", [RoomsController::class, 'getHotelRoomsByType']);
Route::post("/updateRoom", [RoomsController::class, 'updateRoom']);
Route::get("/roomDetail", [RoomsController::class, 'roomDetail'])->middleware('auth:sanctum');
/**image */
Route::post("/addImage", [ImagesController::class, 'addImage'])->middleware('auth:sanctum');
Route::get("/showImage/{id}", [ImagesController::class, 'showImage']);

/**locations */
Route::get('/getAllLocations', [LocationsController::class, 'getAllLocations']);
Route::post('/addLocation', [LocationsController::class, 'addLocation'])->middleware('auth:sanctum');

/**favorites */
Route::post('/addFav', [FavoretsController::class, 'addFav'])->middleware('auth:sanctum');
Route::post('/deleteFav', [FavoretsController::class, 'deleteFav'])->middleware('auth:sanctum');
/**rate */
Route::post('/rate', [RatesController::class, 'rate'])->middleware('auth:sanctum');

/**offer */
Route::get("/myOffers", [OffersController::class, 'myOffers'])->middleware('auth:sanctum');
Route::post('/addOffer', [OffersController::class, 'addOffer'])->middleware('auth:sanctum');
Route::post('/deleteOffer', [OffersController::class, 'deleteOffer'])->middleware('auth:sanctum');

/**search */
Route::get("/search", [HotelsController::class, 'search'])->middleware('auth:sanctum');

/**Booking */
Route::post("/addBooking", [BookingsController::class, 'addBookings'])->middleware('auth:sanctum');
Route::get("/showBooking", [BookingsController::class, 'showBooking'])->middleware('auth:sanctum');
Route::get("/getBookingPrice", [BookingsController::class, 'getBookingPrice']);

//33
