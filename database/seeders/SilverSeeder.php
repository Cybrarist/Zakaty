<?php

namespace Database\Seeders;

use App\Models\Gold;
use App\Models\Silver;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SilverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Silver::factory(10)->createQuietly();

    }
}
