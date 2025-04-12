<?php
namespace App\Models;

use App\Helpers\UUID;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use UUID;

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

    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }
}
