<?php

namespace App\Models;

use App\Helpers\UUID;
use Illuminate\Database\Eloquent\Model;

class Freelancer extends Model
{
    use UUID;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
