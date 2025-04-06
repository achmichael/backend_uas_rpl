<?php

namespace App\Models;

use App\Helpers\UUID;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $table = 'user_profiles';
    protected $fillable = ['id','user_id','full_name','portofolio_url','bio'];

}
