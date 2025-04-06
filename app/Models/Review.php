<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\UUID;

class Review extends Model
{
    use UUID;
    protected $fillable = ['post_id', 'reviewer_id', 'reviewee_id', 'rating', 'comment'];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewee()
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }
}
