<?php

namespace Database\Seeders;

use App\Models\Money;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MoneySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Money::factory(10)->createQuietly();
    }
}
