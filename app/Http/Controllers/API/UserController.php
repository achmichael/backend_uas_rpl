<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\hash;

class UserController extends Controller
{
    public function show($id){
        $user = User::with('user_profile','location','portofolio','catalog','certificate')->find($id);
        if(! $user){
            return response()->json([
                'succes'    => false,
                'message'   => 'user is invalid',
            ]);
        }

        return response()->json([
            'succes'  => true,
            'data'    => $user
        ]);
    }

    public function update(Request $request, $id){
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $user->update($request->all());
        return response()->json([
            'success' => true,
            'data'    => $user,
        ]);
    }

}
