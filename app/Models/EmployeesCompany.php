<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeesCompany extends Model
{
    protected $fillable = [
        'company_id',
        'employee_id',
        'position',
        'status',
    ];

    public function casts (): array
    {
        return [
            'deleted_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at'=> 'created_at',
        ];
    }
    
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
