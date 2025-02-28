<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivityLog extends Model
{
    protected $table = 'user_activity_logs';
        
    protected $fillable = ['user_id', 'activity_type', 'activity_time'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
