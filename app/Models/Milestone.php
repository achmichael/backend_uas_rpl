<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    protected $fillable = ['contract_id', 'milestone_description', 'due_date', 'payment_amount', 'status'];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
