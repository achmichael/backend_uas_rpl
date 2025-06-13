<?php
namespace App\Models;

use App\Helpers\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use UUID, HasFactory;

    protected $fillable = ['contract_type', 'contract_type_id', 'client_id', 'provider_id', 'contract_date', 'status', 'due_date'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function contract_type()
    {
        return $this->morphTo(__FUNCTION__, 'contract_type', 'contract_type_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeProvider($query, $providerId)
    {
        return $query->where('provider_id', $providerId);
    }

    public function casts(): array
    {
        return [
            'contract_date' => 'datetime',
            'due_date'      => 'datetime',
        ];
    }
}
