<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['category_name'];

    public function posts()
    {
        return $this->hasMany(Post::class, 'category_id', 'id');
    }

    public function freelancers()
    {
        return $this->hasMany(Freelancer::class, 'category_id', 'id');
    }
}
