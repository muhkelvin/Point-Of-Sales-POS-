<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bread>
 */
class BreadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'  => $this->faker->randomElement(['Roti Tawar', 'Roti Gandum', 'Roti Manis']),
            'price' => $this->faker->randomFloat(2, 5, 20), // harga antara 5 - 20
            'stock' => $this->faker->numberBetween(50, 200),
        ];
    }
}
