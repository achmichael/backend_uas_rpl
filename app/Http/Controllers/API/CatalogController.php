<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Catalog;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Catalogs",
 *     description="Data terkait katalog produk atau layanan pengguna"
 * )
 *
 * @OA\Schema(
 *     schema="Catalog",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="catalog_name", type="string", example="Web Development Services"),
 *     @OA\Property(property="price", type="number", format="float", example=500000),
 *     @OA\Property(property="description", type="string", example="Professional web development services with modern technologies"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class CatalogController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/catalogs",
     *     summary="Create a new catalog item",
     *     tags={"Catalogs"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"catalog_name", "price", "description"},
     *             @OA\Property(property="catalog_name", type="string", example="Web Development Services"),
     *             @OA\Property(property="price", type="number", format="float", example=500000),
     *             @OA\Property(property="description", type="string", example="Professional web development services with modern technologies")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Catalog created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="succes", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Catalog")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             @OA\Property(property="Success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="invalid create catalog")
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
    public function create(Request $request)
    {
        try {
            $request->validate([
                'catalog_name' => 'required',
                'price'        => 'required',
                'description'  => 'required',
            ]);
            $data            = $request->all();
            $data['user_id'] = auth()->id;
            $catalog         = Catalog::create($data);

            if (! $catalog) {
                return error('invalid create catalog', 404);
            }

            return success($catalog, 'Success create catalog', 201);
        } catch (ValidationException $e) {
            return errorValidation($e->getMessage(), $e->errors(), 422);
        }

    }

    /**
     * @OA\Put(
     *     path="/api/catalogs/{id}",
     *     summary="Update a catalog item",
     *     tags={"Catalogs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Catalog ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"catalog_name", "price", "description"},
     *             @OA\Property(property="catalog_name", type="string", example="Updated Web Development Services"),
     *             @OA\Property(property="price", type="number", format="float", example=600000),
     *             @OA\Property(property="description", type="string", example="Updated professional web development services with modern technologies")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Catalog updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="succes update"),
     *             @OA\Property(property="data", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Catalog not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="catalog not found")
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
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'catalog_name' => 'required',
                'price'        => 'required',
                'description'  => 'required',
            ]);

            $catalog = Catalog::find($id);

            if (! $catalog) {
                return error('catalog not found', 404);
            }

            $catalog->update();

            return success($id, 'succes update catalog', 200);
        } catch (ValidationException $e) {
            return errorValidation($e->getMessage(), $e->errors(), 422);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/catalogs/{id}",
     *     summary="Delete a catalog item",
     *     tags={"Catalogs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Catalog ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Catalog deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Catalog deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Catalog not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="catalog not found")
     *         )
     *     )
     * )
     */
    public function delete($id)
    {
        $catalog = Catalog::find($id);
        if (! $catalog) {
            return error('catalog not found', 404);
        }
        $catalog->delete();
        return success($id, 'Catalog deleted successfully', 200);
    }
}
