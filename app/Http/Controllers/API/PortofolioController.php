<?php

namespace App\Http\Controllers\API;

use App\Models\Portofolio;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class PortofolioController extends Controller
{
    public function portofolio(Request $request){
        try{
        $request->validate([
            'user_id'  => 'required|exists:users,id',
            'title'    => 'required|string',
            'url'      => 'required|string',
        ]);
        $data = $request->all();
        $data['user_id'] = auth()->id();
        $portofolio = Portofolio::create($data);
        if(! $portofolio){
            return response()->json([
                'succes'    => false,
                'message'   => 'invalid check the required again',
            ]);

        }
            return response()->json([
                'succes'    => true,
                'data'   => $portofolio,
            ]);
        }catch(ValidationException $e){
            return response()->json([
                'message'   => $e->getMessage(),
                'errors'    => $e->errors(),
            ]);
        }
    }

    public function update(Request $request,$id){
        try{
            $request->validate([
                'user_id'  => 'required|exists:users,id',
                'title'    => 'required|string',
                'url'      => 'required|string',
            ]);

            $portofolio = Portofolio::find($id);
            if(! $portofolio){
                return response()->json([
                    'succes'    => false,
                    'message'   => 'invalid check the required again',
                ]);

            }
            $portofolio->updated($request->all());
            return response()->json([
                'status'    => 'succes',
                'data'      => $portofolio,
            ]);

        }catch(ValidationException $e){
            return response()->json([
            'massage' => $e->getMessage(),
            'error'  => $e->errors(),
            ],422);
        }
    }

    public function delete($id){
        $portofolio = Portofolio::find($id);
        if(! $portofolio){
            return response()->json([
                'massage' => 'error',
            ]);
        }

        $portofolio->delete($id);
        return response()->json([
            'status'  => 'success',
            'message' => 'Portofolio deleted successfully',
        ]);

    }

}
