<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\UUID;

class Post extends Model
{
    use UUID;

    protected $fillable = ['posted_by', 'title', 'description', 'price', 'number_of_employee', 'category_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
