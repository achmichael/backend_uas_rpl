<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\UUID;

class Contract extends Model
{
    use UUID;

    protected $fillable = ['post_id', 'client_id', 'provider_id', 'contract_date', 'status'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }
}
