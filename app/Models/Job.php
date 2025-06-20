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
    protected $guarded = ['id'];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function contract()
    {
        return $this->morphOne(Contract::class, 'contractable');
    }

}
