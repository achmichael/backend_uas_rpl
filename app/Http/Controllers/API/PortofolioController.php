<?php

namespace App\Http\Controllers\API;

use App\Models\Portofolio;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Portfolios",
 *     description="Data terkait portofolio pengguna"
 * )
 *
 * @OA\Schema(
 *     schema="Portfolio",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="E-commerce Website"),
 *     @OA\Property(property="url", type="string", example="https://example.com/portfolio/ecommerce"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

class PortofolioController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/portfolios",
     *     summary="Create a new portfolio item",
     *     tags={"Portfolios"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "title", "url"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="title", type="string", example="E-commerce Website"),
     *             @OA\Property(property="url", type="string", example="https://example.com/portfolio/ecommerce")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Portfolio created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="succes", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Portfolio")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             @OA\Property(property="succes", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="invalid check the required again")
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
            'user_id'  => 'required|exists:users,id',
            'title'    => 'required|string',
            'url'      => 'required|string',
        ]);
        $data = $request->all();
        $data['user_id'] = auth()->id;
        $portofolio = Portofolio::create($data);
        if(! $portofolio){
            return error('invalid create portofolio',404);
        }
        return success($portofolio,'succes create portofolio',201);
        }catch(ValidationException $e){
            return errorValidation($e->getMessage(),$e->errors(),404);
        }
    }


    /**
     * @OA\Put(
     *     path="/api/portfolios/{id}",
     *     summary="Update a portfolio item",
     *     tags={"Portfolios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Portfolio ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "title", "url"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="title", type="string", example="Updated E-commerce Website"),
     *             @OA\Property(property="url", type="string", example="https://example.com/portfolio/updated-ecommerce")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Portfolio updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="succes"),
     *             @OA\Property(property="data", ref="#/components/schemas/Portfolio")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             @OA\Property(property="succes", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="invalid check the required again")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="massage", type="string"),
     *             @OA\Property(property="error", type="object")
     *         )
     *     )
     * )
     */

    public function update(Request $request,$id){
        try{
            $request->validate([
                'user_id'  => 'required|exists:users,id',
                'title'    => 'required|string',
                'url'      => 'required|string',
            ]);

            $portofolio = Portofolio::find($id);
            if(! $portofolio){
                return error('portofolio not found',404);


            }
            $portofolio->updated($request->all());
            return success($id,'success update portofolio',200);

        }catch(ValidationException $e){
           return errorValidation($e->getMessage(),$e->errors(),422);
        }
    }


    /**
     * @OA\Delete(
     *     path="/api/portfolios/{id}",
     *     summary="Delete a portfolio item",
     *     tags={"Portfolios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Portfolio ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Portfolio deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Portofolio deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Portfolio not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="massage", type="string", example="error")
     *         )
     *     )
     * )
     */

    public function delete($id){
        $portofolio = Portofolio::find($id);
        if(! $portofolio){
          return error('portofolio not found',404);
        }

        $portofolio->delete($id);
        return success($id,'delete has already succesfully',200);

    }

}
