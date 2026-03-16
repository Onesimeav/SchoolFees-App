<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Fee>
 */
class FeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['RegistrationFee', 'TuitionFee', 'GeneralFee'];

        return [
            'type' => fake()->randomElement($types),
            'total_amount' => fake()->numberBetween(1000, 10000),
            'academic_year' => '2024-2025',
            'title' => fake()->words(3, true) . ' Fee',
            'classroom' => 'Grade ' . fake()->numberBetween(1, 12) . fake()->randomElement(['A', 'B', 'C']),
            'description' => fake()->sentence(),
            'number_of_installments' => null,
            'required' => fake()->boolean(),
        ];
    }
}
