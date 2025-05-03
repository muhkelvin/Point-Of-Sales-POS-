<?php

namespace Database\Factories;

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
        $totalItems = $this->faker->numberBetween(1, 10);
        $totalPrice = $this->faker->randomFloat(2, 20, 100);
        $payment = $totalPrice + $this->faker->randomFloat(2, 0, 20);
        return [
            'total_items' => $totalItems,
            'total_price' => $totalPrice,
            'payment'     => $payment,
            'change'      => $payment - $totalPrice,
        ];
    }
}
