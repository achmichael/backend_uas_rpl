<?php

namespace App\Models;

use App\Helpers\UUID;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use UUID;
    protected $table = 'jobs_table';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = [];

    public function post()
    {
        return $this->belongsTo(Post::class, 'id_post');
    }

    public function contract()
    {
        return $this->morphOne(Contract::class, 'contract_type');
    }

}
