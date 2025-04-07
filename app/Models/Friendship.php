<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\UUID;

class Friendship extends Model
{
    use UUID;
    protected $guarted = [];

    public function self(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function other(){
        return $this->belongsTo(User::class, 'friend_id');
    }


}
