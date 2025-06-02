<?php

namespace App\Models;

use App\Helpers\UUID;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\FuncCall;

class Company extends Model
{
    use UUID;
    protected $guarded = ['id'];
    
    protected $casts = [
        'social_links' => 'json',
        'founded_at' => 'datetime'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function employees()
    {
        return $this->hasMany(EmployeesCompany::class);
    }
}

