<?php

namespace Database\Seeders;

use App\Models\Bread;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed beberapa jenis roti
        Bread::factory()->count(5)->create();
    }
}
