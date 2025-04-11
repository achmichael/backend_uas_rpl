<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentCallback extends Model
{
    protected $table = 'payments_callback';

    protected $fillable = [
        'payment_id',
        'provider',
        'callback_data',
        'status',
        'received_at',
    ];

    protected $casts = [
        'callback_data' => 'array',
        'received_at' => 'datetime',
    ];

    public function payment(){
        return $this->belongsTo(Payment::class, 'payment_id');
    }
}
