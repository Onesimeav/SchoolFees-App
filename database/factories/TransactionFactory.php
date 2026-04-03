<?php

namespace Database\Factories;

use App\Models\Fee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'fee_id' => Fee::factory(),
            'amount' => fake()->numberBetween(100, 5000),
            'date' => fake()->date(),
            'status' => fake()->randomElement(['pending', 'completed', 'failed', 'refunded']),
            'kkiapay_reference' => 'KK-' . fake()->uuid(),
            'phone_number' => fake()->phoneNumber(),
        ];
    }
}
