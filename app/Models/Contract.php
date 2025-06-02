<?php
namespace App\Models;

use App\Helpers\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contract extends Model
{
    use UUID, HasFactory;

    protected $fillable = ['contract_type', 'contract_type_id', 'client_id', 'provider_id', 'contract_date', 'status'];

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

}
