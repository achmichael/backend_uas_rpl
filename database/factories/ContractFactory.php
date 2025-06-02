<?php
namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contract>
 */
class ContractFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id'        => User::pluck('id')->random(),
            'contract_type'    => 'App\\Models\\Post',
            'contract_type_id' => Post::pluck('id')->random(),
            'provider_id'      => User::pluck('id')->random(),
            'contract_date'    => $this->faker->date(),
            'status'           => $this->faker->randomElement(['active', 'completed', 'terminated']),
        ];
    }

    /**
     * Indicate that the contract is active.
     *
     * @return $this
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'active',
            ];
        });
    }

    /**
     * Indicate that the contract is completed.
     *
     * @return $this
     */
    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'completed',
            ];
        });
    }

    /**
     * Indicate that the contract is terminated.
     *
     * @return $this
     */
    public function terminated()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'terminated',
            ];
        });
    }
}
