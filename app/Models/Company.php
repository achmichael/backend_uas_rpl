<?php

namespace App\Models;

use App\Helpers\UUID;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\FuncCall;

class Company extends Model
{
    use UUID;
    protected $guarded = ['id'];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function job(){
        return $this->belongsTo(Job::class, 'job_id'  );
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function employees()
    {
        return $this->hasMany(EmployeesCompany::class);
    }
}

