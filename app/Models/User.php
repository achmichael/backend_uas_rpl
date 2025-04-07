<?php
namespace App\Models;

use App\Helpers\UUID;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, UUID;

    protected $primaryKey = 'id';
    protected $keyType    = 'string';
    public $incrementing  = false;

    protected $fillable = ['username', 'email', 'password', 'role_id', 'remember_token', 'phone_number', 'profile_picture', 'is_verified'];

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

    public function posts()
    {
        return $this->hasMany(Post::class, 'posted_by', 'id');
    }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
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

    public function certificates(){
        return $this->hasMany(Certificate::class);
    }


    public function self(){
        return $this->belongsToMany(User::class, 'friendships','user_id');
    }
}
