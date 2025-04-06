<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Certificate;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class CertificateController extends Controller
{
    public function certificate(Request $request)
    {
        try {
            $request->validate([
                'certificate_name'  => 'required|string',
                'expiration_date'   => 'required|date',
                'category'          => 'required',
                'status'            => 'required|string',
                'file_path'         => 'required',
                'description'       => 'required',
            ]);
            $data = $request->all();
            $data['user_id'] = auth()->id();
            $certificate = Certificate::create($data);
            if (!$certificate) {
                return response()->json([
                    'status' => false,
                    'message' => 'invalid add certificate'
                ]);
            }
            return response()->json([
                'status' => true,
                'data' => $certificate,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ]);
        }

    }

    public function delete($id){
        {
            $certificate = Certificate::find($id);
            if(! $certificate){
                return response()->json([
                    'massage' => 'certificate is nothing',
                ]);
            }
            $certificate->delete();
            return response()->json([
                'status' => 'succes',
                'message' => 'delete succesfully'
            ]);
        }
    }

    public function update(Request $request,$id){
        try{
            $request->validate([
                    'certificate_name'  => 'required|string',
                    'expiration_date'   => 'required|date',
                    'category'          => 'required',
                    'status'            => 'required|string',
                    'file_path'         => 'required',
                    'description'       => 'required',

            ]);

            $certificate = Certificate::find($id);

            if (!$certificate) {
                return response()->json([
                    'status' => false,
                    'message' => 'invalid add certificate'
                ]);
            }
            $certificate->update($request->all());
            return response()->json([
                'ststus'    => 'succes',
                'data'      => $certificate,
            ]);
        }catch(ValidationException $e){
            return response()->json([
                'massage' => $e->getMessage(),
                'error'  => $e->errors(),
            ],422);
        }
    }






}
