<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'applicant_id' => User::factory()->create(['role_id' => 2])->id, // Client role
            'apply_file' => $this->faker->url() . '/resume.pdf',
            'amount' => $this->faker->numberBetween(1000, 10000),
            'status' => $this->faker->randomElement(['pending', 'accepted', 'rejected']),
        ];
    }

    /**
     * Indicate that the application is pending.
     *
     * @return $this
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
            ];
        });
    }

    /**
     * Indicate that the application is accepted.
     *
     * @return $this
     */
    public function accepted()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'accepted',
            ];
        });
    }

    /**
     * Indicate that the application is rejected.
     *
     * @return $this
     */
    public function rejected()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'rejected',
            ];
        });
    }
}
