<?php

namespace App\Models;

use App\Helpers\UUID;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, UUID;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['username', 'email', 'password', 'role_id', 'remember_token'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function freelancer()
    {
        return $this->hasOne(Freelancer::class);
    }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     *
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function user_profile(){
        return $this->hasOne(Profile::class);
    }

    public function location(){
        return $this->hasOne(Location::class);
    }

    public function portofolio(){
        return $this->hasOne(Portofolio::class);
    }

    public function catalog(){
        return $this->hasMany(Catalog::class);
    }

    public function certificate(){
        return $this->hasMany(Certificate::class);
    }


    public function friendofmine(){
        return $this->belongsToMany(User::class, 'friendships','user_id','friend_id')
                    ->withPivot('status')->withTimestamps;
    }

    public function myfriend(){
        return $this->belongsToMany(User::class,'friendships','friend_id','user_id')
                    ->withPivot('status')->withTimestamps;
    }

    public function friend(){
        return $this->self()->wherePivot('status', 'accepted')->get()
                    ->merge ($this->friend()->wherePivot('status', 'accepted')->get());
    }
}
