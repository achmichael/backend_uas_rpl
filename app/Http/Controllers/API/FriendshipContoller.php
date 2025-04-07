<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FriendshipContoller extends Controller
{
    public function addfriend($friendId){
        $user = auth()->user();

        $exists = \DB::table('friendships')->where(function($query) use ($user, $friendId) {
            $query->where('user_id', $user->id)
                  ->where('friend_id', $friendId);
        })->orWhere('friendships')->where(function ($query) use ($user,$friendId){
            $query->where('friend_id', $friendId)
                  ->where('user_id',$user->id);
        })->exists();

        if($exists){
            return response()->json([
                'message'   => 'friend request has already exists'
            ]);
        }

        \DB::table('friendship')->insert([
            'user_id'   => $user->id,
            'friend_id' => $friendId,
            'status'    => 'pending',
        ]);

        return response()->json([
            'message'   => 'request request sent!'
        ]);
    }

    public function accept($friendId){
        $user = auth()->user();

        \DB::table('friendship')->where([
            'user_id'   => $user->id,
            'friend_id' => $friendId,
        ])->update(['status' => 'accepted']);

        return response()->json([
            'message'   => 'friend request accepted!'
        ]);
    }
}
