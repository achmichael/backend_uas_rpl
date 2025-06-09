<?php

namespace App\Models;

use App\Helpers\UUID;
use Illuminate\Database\Eloquent\Model;

class Portofolio extends Model
{
    use UUID;
    protected $fillable  = ['id','user_id','title','url'];
}
