<?php

namespace App\Http\Controllers\API;

use App\Models\UserProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class UserProfileController extends Controller
{
    public function profile(Request $request){
        try{
            $request->validate([
                'full_name'         => 'required|string',
                'portofolio_url'    => 'required|string',
                'bio'               => 'required|string',
            ]);
            $data = $request->all();
            $data['user_id'] = auth()->id();
            $profile = UserProfile::create($data);

            if(! $profile){
                return response()->json([
                    'succes'    => false,
                    'message'   => 'input invalid',
                ]);
            }

            return response()->json([
                'succes'    => true,
                'data'      => $profile,
            ]);
        }catch(ValidationException $e){
            return response()->json([
                'message'   => $e->getMessage(),
                'errors'    => $e->errors(),
            ]);
        }
    }
}
