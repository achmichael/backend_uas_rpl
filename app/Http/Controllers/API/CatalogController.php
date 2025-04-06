<?php

namespace App\Http\Controllers\API;

use App\Models\Catalog;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class CatalogController extends Controller
{
    public function catalog(Request $request){
        try{
            $request->validate([
                'catalog_name'  => 'required',
                'price'         => 'required',
                'description'   => 'required',
            ]);
            $data = $request->all();
            $data['user_id'] = auth()->id();
            $catalog = Catalog::create($data);

            if(! $catalog){
                return response()->json([
                    'Success'   => false,
                    'message'   => 'invalid create catalog',
                ]);
            }

            return response()->json([
                'succes'    => true,
                'data'  => $catalog,
            ]);
        }catch(ValidationException $e){
            return response()->json([
                'message'       => $e->getMessage(),
                'errors'        => $e->errors(),
            ]);
        }

    }

    public function update(Request $request,$id){
        try{
            $request->validate([
                'catalog_name'  => 'required',
                'price'         => 'required',
                'description'   => 'required',
            ]);

            $catalog = Catalog::find($id);

            if(! $catalog){
                return response()->json([
                    'message'   => 'catalog not found',
                ]);
            }
            $catalog->update();
            return response()->json([
                'status'    => 'succes update',
                'data'  => $id,
            ]);
        }catch(ValidationException $e){
            return response()->json([
                'message'       => $e->getMessage(),
                'errors'        => $e->errors(),
            ]);
        }
    }

    public function delete($id){
        $catalog = Catalog::find($id);
        if(! $catalog){
            return response()->json([
                'message'   => 'catalog not found',
            ]);
        }
        $catalog->delete();
        return response()->json([
            'status'  => 'success',
            'message' => 'Catalog deleted successfully',
        ]);
    }
}
