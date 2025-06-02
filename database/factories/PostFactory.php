<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'posted_by' => User::factory(),
            'category_id' => function() {
                // If Category model exists and has a factory, use it, otherwise return 1
                if (class_exists(Category::class) && method_exists(Category::class, 'factory')) {
                    return Category::factory();
                }
                return 1;
            },
            'price' => $this->faker->numberBetween(100, 10000),
            'level_id' => 1,
            'required_skills' => json_encode(['PHP', 'Laravel', 'MySQL']),
            'min_experience_years' => 3,
        ];
    }    /**
     * Indicate that the post is open for applications.
     *
     * @return $this
     */
    public function open()
    {
        return $this; // Status column not in schema, returning self without changes
    }

    /**
     * Indicate that the post is closed.
     *
     * @return $this
     */
    public function closed()
    {
        return $this; // Status column not in schema, returning self without changes
    }

    /**
     * Indicate that the post is in progress.
     *
     * @return $this
     */
    public function inProgress()
    {
        return $this; // Status column not in schema, returning self without changes
    }
}
