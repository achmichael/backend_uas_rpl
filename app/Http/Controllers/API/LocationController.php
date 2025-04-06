<?php

namespace App\Http\Controllers\API;

use App\Models\Location;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Validation\ValidationException;

class LocationController extends Controller
{
    public function location(Request $request){
        // dd($request->all());
       try{
        $request->validate([
            'accuracy'  => 'required',
            'latitude'  => 'required',
            'longitude' => 'required',
            'altitude'   => 'required',
            'heading'   => 'required',
            'speed'     => 'required',
            'altitudeAccuracy' => 'required',
        ]);
        $data = $request->all();
        $data['user_id'] = auth()->id();
        $location = Location::create($data);
        return response()->json([
            'succes' => true,
            'data'   => $location,
        ]);
    }catch(ValidationException $e){
        return response()->json([
            'message'=> $e->getMessage(),
            'errors' => $e->errors(),
        ],422);
    }

    }

    public function update(Request $request,$id){
    try{
            $request->validate([
                'accuracy'  => 'required',
                'latitude'  => 'required',
                'longitude' => 'required',
                'altitude'   => 'required',
                'heading'   => 'required',
                'speed'     => 'required',
                'altitudeAccuracy' => 'required',
            ]);
            $location = Location::find($id);
            if(! $location){
            return response()->json([
                'message' => 'Location not found',
            ], 404);
            }

        $location->update($request->all());
        return response()->json([
            'status' => 'success',
            'data'   => $location,
        ]);
        }catch(ValidationException $e){
        return response()->json([
            'message' => $e->getMessage(),
            'errors'  => $e->errors(),
        ], 422);
     }
    }

    public function delete($id){
        $location = Location::find($id);
        if(! $location){
            return response()->json([
                'message'   => 'location with that id is empty'
            ]);
        }
        $location->delete();
        return response()->json([
            'status'  => 'success',
            'message' => 'Location deleted successfully',
        ]);
    }
}
