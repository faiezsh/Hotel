<?php

namespace App\Http\Controllers;

use App\Models\Images;
use App\Models\Rooms;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ImagesController extends Controller
{
    public function addImage(Request $request)
    {
        try {
            $id = Auth::id();
            $file = Validator::make($request->all(), [
                'image' => 'required | file'
            ]);
            $path = $request->file('image');
            $filename = time() . $path->getClientOriginalName();
            Storage::disk('public')->put($filename, File::get($path));
            $image = Images::create([
                'userId' => $id,
                'image' => $filename,
                'type' => 'preview'
            ]);
            return response()->json([
                'status' => true,
                'massege' => 'sucssfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'massage' => $th->getMessage()
            ], 500);
        }
    }
    public function showImage($id)
    {
        try {
            $request = Images::where('id', $id)->first();
            $image = Storage::disk('public')->get($request->image);
            if ($image != null) {
                return (new Response($image, 200));
            }
        } catch (\Throwable $th) {
            return response()->json([
                'state' => false,
                'data' => $th->getMessage()
            ], 500);
        }
    }
}
