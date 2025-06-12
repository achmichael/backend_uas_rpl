<?php

namespace App\Services;

use App\Models\Freelancer;
use Illuminate\Database\Eloquent\Collection;

class PostService
{
    public function matchingFreelancer($post): Collection
    {
        $freelancers = Freelancer::where('category_id', $post->category_id)
            ->where('experience_years', '>=', $post->number_of_employee)
            ->get()
            ->filter(function ($freelancer) use ($post) {
                return count(array_intersect($freelancer->skills, $post->required_skills)) > 0;
            });

        return $freelancers->sortByDesc('rating');
    }
}
