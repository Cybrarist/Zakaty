<?php

namespace Database\Seeders;

use App\Enums\UserRoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
//        $this->call([
//            CurrencySeeder::class,
//        ]);

//        if (app()->isLocal()){
//
//            $this->call([
//                GoldSeeder::class,
//                SilverSeeder::class,
//            ]);
//
//            User::create([
//                'name' => 'Test',
//                'email' => 'test@test.com',
//                'password' => 'password',
//                'role' => UserRoleEnum::Admin,
//                'currency_id' => 1
//            ]);
//        }
//
        $this->call([
            MoneySeeder::class,
            GoldSeeder::class,
            SilverSeeder::class,
        ]);

        Artisan::call('exchange:refresh-all');
    }
}
