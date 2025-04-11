<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'type',
        'provider',
        'account_number',
        'currency',
        'status',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
