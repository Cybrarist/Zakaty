<?php

namespace Database\Seeders;

use App\Models\Gold;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GoldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Gold::factory(10)->createQuietly();
    }
}
