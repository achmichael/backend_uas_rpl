<?php

namespace App\Models;

use App\Helpers\UUID;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use UUID;
     protected $fillable = ['user_id','accuracy', 'latitude', 'longitude', 'altitude', 'heading', 'speed', 'altitudeAcuracy'] ;

    public function user(){
        return $this->hasOne(User::class, 'user_id');
    }


}
