<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = ['post_id', 'applicant_id', 'apply_file', 'amount', 'status'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function applicant()
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    public function scopeChangeStatus($query, $status)
    {
        return $query->update(['status' => $status]);
    }
}
