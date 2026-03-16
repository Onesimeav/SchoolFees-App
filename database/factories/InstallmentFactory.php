<?php

namespace Database\Factories;

use App\Models\Fee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Installment>
 */
class InstallmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tuition_fee_id' => Fee::factory()->create(['type' => 'TuitionFee']),
            'number' => fake()->numberBetween(1, 5),
            'amount' => fake()->numberBetween(500, 2000),
            'due_date' => fake()->date(),
        ];
    }
}
