<?php

namespace App\Models;

use App\Helpers\UUID;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use UUID;

    protected $fillable = [
        'contract_id', 
        'payment_method_id', 
        'payment_date', 
        'amount', 
        'payment_status',
        'transaction_id',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function paymentMethod(){
        return $this->belongsTo(PaymentMethod::class);
    }

    public function paymentCallback()
    {
        return $this->hasOne(PaymentCallback::class, 'payment_id');
    }
}
