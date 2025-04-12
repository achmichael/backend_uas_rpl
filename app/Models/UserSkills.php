<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// pivot table for store relation between users and skills
class UserSkills extends Model
{
    protected $table = 'user_skills';
    protected $fillable = ['user_id', 'skill_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class, 'skill_id');
    }
}
