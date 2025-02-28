<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $table = 'user_profiles';

    protected $fillable = ['user_id', 'full_name', 'portofolio_url', 'bio'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
