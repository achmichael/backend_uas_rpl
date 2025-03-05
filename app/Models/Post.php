<?php
namespace App\Models;

use App\Helpers\UUID;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use UUID;

    // protected $fillable = ['posted_by', 'title', 'description', 'price', 'number_of_employee', 'category_id'];

    protected $guarded = [];

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

    protected $casts = [ // add this line to your model to cast the required_skills attribute to an array in the code and to a JSON when insert to database and reconverting it to an array when fetching from the database
        'required_skills' => 'array',
    ];
}
