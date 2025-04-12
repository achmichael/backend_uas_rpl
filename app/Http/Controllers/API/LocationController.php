<?php

namespace App\Http\Controllers\API;

use App\Models\Location;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Locations",
 *     description="Data terkait lokasi pengguna"
 * )
 *
 * @OA\Schema(
 *     schema="Location",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="accuracy", type="number", format="float", example=10.5),
 *     @OA\Property(property="latitude", type="number", format="float", example=-6.2088),
 *     @OA\Property(property="longitude", type="number", format="float", example=106.8456),
 *     @OA\Property(property="altitude", type="number", format="float", example=20.5),
 *     @OA\Property(property="heading", type="number", format="float", example=90.0),
 *     @OA\Property(property="speed", type="number", format="float", example=5.0),
 *     @OA\Property(property="altitudeAccuracy", type="number", format="float", example=1.5),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class LocationController extends Controller
{
     /**
     * @OA\Post(
     *     path="/api/locations",
     *     summary="Create a new location record",
     *     tags={"Locations"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"accuracy", "latitude", "longitude", "altitude", "heading", "speed", "altitudeAccuracy"},
     *             @OA\Property(property="accuracy", type="number", format="float", example=10.5),
     *             @OA\Property(property="latitude", type="number", format="float", example=-6.2088),
     *             @OA\Property(property="longitude", type="number", format="float", example=106.8456),
     *             @OA\Property(property="altitude", type="number", format="float", example=20.5),
     *             @OA\Property(property="heading", type="number", format="float", example=90.0),
     *             @OA\Property(property="speed", type="number", format="float", example=5.0),
     *             @OA\Property(property="altitudeAccuracy", type="number", format="float", example=1.5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Location created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="succes", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Location")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function create(Request $request){
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
        $data['user_id'] = auth()->id;
        $location = Location::create($data);
        return success($location,'create location successfully',201);
    }catch(ValidationException $e){
        return errorValidation($e->getMessage(),$e->errors(),422);
    }
    }

    /**
     * @OA\Put(
     *     path="/api/locations/{id}",
     *     summary="Update a location record",
     *     tags={"Locations"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Location ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"accuracy", "latitude", "longitude", "altitude", "heading", "speed", "altitudeAccuracy"},
     *             @OA\Property(property="accuracy", type="number", format="float", example=12.5),
     *             @OA\Property(property="latitude", type="number", format="float", example=-6.2090),
     *             @OA\Property(property="longitude", type="number", format="float", example=106.8460),
     *             @OA\Property(property="altitude", type="number", format="float", example=22.5),
     *             @OA\Property(property="heading", type="number", format="float", example=95.0),
     *             @OA\Property(property="speed", type="number", format="float", example=6.0),
     *             @OA\Property(property="altitudeAccuracy", type="number", format="float", example=2.0)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Location updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/Location")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Location not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Location not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */

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
            return error('location not found',404);
            }

        $location->update($request->all());
        return success($location,'location update successfully');
        }catch(ValidationException $e){
        return errorValidation($e->getMessage(),$e->errors(),422);
     }
    }

    /**
     * @OA\Delete(
     *     path="/api/locations/{id}",
     *     summary="Delete a location record",
     *     tags={"Locations"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Location ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Location deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Location deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Location not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="location with that id is empty")
     *         )
     *     )
     * )
     */


     
    public function delete($id){
        $location = Location::find($id);
        if(! $location){
            return error('location with that id is empty');
        }
        $location->delete();
        return success($location,'location delete successfully');
    }
}
