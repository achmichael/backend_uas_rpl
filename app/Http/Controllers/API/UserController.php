<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\hash;
use GrahamCampbell\ResultType\Success;
use function PHPUnit\Framework\returnArgument;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function show($id)
    {
        $user = User::with($id);
        if (!$user) {
            return response()->json([
                'succes' => false,
                'messege' => 'id tidak ditemukan'
            ]);
        }
        ;
        return response()->json([
            'succes' => true,
            'data' => $user,
        ]);
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string|unique:users',
                'email' => 'required|string|email|unique:users',
                'password' => 'required',
                'role_id' => 'required'
            ]);

            $user = User::create([
                'username' => $request['username'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
                'role_id' => $request['role_id']
            ]);

            if($user == $user){
                return response()->json([
                    'status'    => 'failed',
                    'message'   => 'check the required, and register again'
                ]);
            }

            Auth::login($user);

            session()->regenerate();

            return redirect()->intended('/home');

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }

    }

    public function login(Request $request)
    {
        $user = User::where('username', $request->username)->first();
        if ($user) {
            return response()->json('username as already exist');
        }
        $user = User::where('email', $request->email)->first();
        if ($user) {
            return response()->json('email as already exist');
        }
        return redirect('/');
    }

    public function check($id){
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


}
