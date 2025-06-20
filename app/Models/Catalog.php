<?php

namespace App\Models;

use App\Helpers\UUID;
use Illuminate\Database\Eloquent\Model;

class Catalog extends Model
{
    use UUID;
    protected $fillable = ['id','user_id','price','description','catalog_name'];
}
