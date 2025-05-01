<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Certificates",
 *     description="Data terkait sertifikat pengguna"
 * )
 *
 * @OA\Schema(
 *     schema="Certificate",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="certificate_name", type="string", example="AWS Certified Developer"),
 *     @OA\Property(property="expiration_date", type="string", format="date", example="2025-12-31"),
 *     @OA\Property(property="category", type="string", example="Cloud Computing"),
 *     @OA\Property(property="status", type="string", example="active"),
 *     @OA\Property(property="file_path", type="string", example="certificates/aws-dev-cert.pdf"),
 *     @OA\Property(property="description", type="string", example="Certification for AWS developer associate level"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class CertificateController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/certificates",
     *     summary="Create a new certificate",
     *     tags={"Certificates"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"certificate_name", "expiration_date", "category", "status", "file_path", "description"},
     *             @OA\Property(property="certificate_name", type="string", example="AWS Certified Developer"),
     *             @OA\Property(property="expiration_date", type="string", format="date", example="2025-12-31"),
     *             @OA\Property(property="category", type="string", example="Cloud Computing"),
     *             @OA\Property(property="status", type="string", example="active"),
     *             @OA\Property(property="file_path", type="string", example="certificates/aws-dev-cert.pdf"),
     *             @OA\Property(property="description", type="string", example="Certification for AWS developer associate level")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Certificate created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Certificate")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="invalid add certificate")
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
    public function certificate(Request $request)
    {
        try {
            $request->validate([
                'certificate_name' => 'required|string',
                'expiration_date'  => 'required|date',
                'category'         => 'required',
                'status'           => 'required|string',
                'file_path'        => 'required',
                'description'      => 'required',
            ]);
            $data            = $request->all();
            $data['user_id'] = auth()->id;
            $certificate     = Certificate::create($data);
            if (! $certificate) {
                return error('invalid create certificate', 400);
            }
            return success($certificate, 'Success create certificate', 201);

        } catch (ValidationException $e) {
            return errorValidation($e->getMessage(), $e->errors());
        }

    }

    /**
     * @OA\Delete(
     *     path="/api/certificates/{id}",
     *     summary="Delete a certificate",
     *     tags={"Certificates"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Certificate ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Certificate deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="succes"),
     *             @OA\Property(property="message", type="string", example="delete succesfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Certificate not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="massage", type="string", example="certificate is nothing")
     *         )
     *     )
     * )
     */
    public function delete($id)
    {
        {
            $certificate = Certificate::find($id);
            if (! $certificate) {
                return error('certificate is nothing', 404);
            }
            $certificate->delete();
            return success($id, 'delete succesfully', 200);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/certificates/{id}",
     *     summary="Update a certificate",
     *     tags={"Certificates"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Certificate ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"certificate_name", "expiration_date", "category", "status", "file_path", "description"},
     *             @OA\Property(property="certificate_name", type="string", example="Updated AWS Certified Developer"),
     *             @OA\Property(property="expiration_date", type="string", format="date", example="2026-12-31"),
     *             @OA\Property(property="category", type="string", example="Cloud Computing"),
     *             @OA\Property(property="status", type="string", example="active"),
     *             @OA\Property(property="file_path", type="string", example="certificates/updated-aws-dev-cert.pdf"),
     *             @OA\Property(property="description", type="string", example="Updated certification for AWS developer associate level")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Certificate updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="ststus", type="string", example="succes"),
     *             @OA\Property(property="data", ref="#/components/schemas/Certificate")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="invalid add certificate")
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
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'certificate_name' => 'required|string',
                'expiration_date'  => 'required|date',
                'category'         => 'required',
                'status'           => 'required|string',
                'file_path'        => 'required',
                'description'      => 'required',
            ]);

            $certificate = Certificate::find($id);

            if (! $certificate) {
                return error('Certificate not found', 404);
            }
            $certificate->update($request->all());
            return success($id, 'Certificate updated successfully', 200);
        } catch (ValidationException $e) {
            return errorValidation($e->getMessage(), $e->errors(), 422);
        }
    }
}
