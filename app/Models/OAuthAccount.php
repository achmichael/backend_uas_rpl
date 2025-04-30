<?php

namespace App\Models;

use App\Helpers\UUID;
use Illuminate\Database\Eloquent\Model;

class OAuthAccount extends Model
{
    use UUID;
    protected $table = 'oauth_accounts';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = ['id'];
}
