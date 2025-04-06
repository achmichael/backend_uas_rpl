<?php

namespace App\Models;

use App\Helpers\UUID;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use UUID;
    protected $fillable = ['id','user_id','certificate_name','expiration_date','category','status','file_path','description',];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
