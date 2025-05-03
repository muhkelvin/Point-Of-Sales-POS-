<?php

namespace Database\Factories;

use App\Models\Bread;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransactionItem>
 */
class TransactionItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ambil roti acak
        $bread = Bread::inRandomOrder()->first() ?? Bread::factory()->create();

        $quantity = $this->faker->numberBetween(1, 5);
        return [
            'transaction_id' => Transaction::factory(), // jika belum ada transaksi, maka factory akan membuat
            'bread_id'       => $bread->id,
            'quantity'       => $quantity,
            'price'          => $bread->price, // harga yang berlaku pada saat transaksi
        ];
    }
}
