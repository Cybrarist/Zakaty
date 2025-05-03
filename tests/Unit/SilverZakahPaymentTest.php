<?php

namespace Tests\Unit;

use App\Enums\KaratEnum;
use App\Enums\PreciousMetalTypeEnum;
use App\Enums\WeightEnum;
use App\Helpers\ZakahHelper;
use App\Models\Currency;
use App\Models\ExchangePrice;
use App\Models\Silver;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Tests\TestCase;

class SilverZakahPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Currency::create([
            'name' => 'USD',
            'code' => 'USD',
            'rate' => 1,
        ]);

        // set the min nisab
        Cache::put('today_minimum_nisab', 100);

        ExchangePrice::create([
            'name' => 'silver_price_24' ,
            'value' => 10
        ]);

        $this->actingAs( User::factory()->create());

    }

    public function test_silver_with_jewellery_considered_payment_amount(): void
    {
        $silvers=[
            [
                'name'=>Str::random() ,
                'weight' => 100,
                'usd_amount' => 1000,
                'weight_unit' => WeightEnum::Gram,
                'karat' => KaratEnum::Karat24,
                'user_id' => 1,
                'type' => PreciousMetalTypeEnum::Ring,
                'is_jewellery' => true,
            ],
            [
                'name'=>Str::random() ,
                'weight' => 100,
                'usd_amount' => 1000,
                'weight_unit' => WeightEnum::Gram,
                'karat' => KaratEnum::Karat24,
                'user_id' => 1,
                'type' => PreciousMetalTypeEnum::Other,
                'is_jewellery' => false,
            ],
        ];

        foreach ($silvers as $silver){
            Silver::create($silver);
        }

        $silver_value= ZakahHelper::get_silver_value_that_is_applicable_for_zakah(User::first());


        $this->assertEquals(2000, $silver_value);
    }


    public function test_silver_without_jewellery_considered_payment_amount(): void
    {
        $silvers=[
            [
                'name'=>Str::random() ,
                'weight' => 100,
                'usd_amount' => 1000,
                'weight_unit' => WeightEnum::Gram,
                'karat' => KaratEnum::Karat24,
                'user_id' => 1,
                'type' => PreciousMetalTypeEnum::Ring,
                'is_jewellery' => true,
            ],
            [
                'name'=>Str::random() ,
                'weight' => 100,
                'usd_amount' => 1000,
                'weight_unit' => WeightEnum::Gram,
                'karat' => KaratEnum::Karat24,
                'user_id' => 1,
                'type' => PreciousMetalTypeEnum::Other,
                'is_jewellery' => false,
            ],
        ];

        User::first()->update([
            'settings' =>[
                'consider_jeweleries_in_zakah' => false
            ]
        ]);

        foreach ($silvers as $silver){
            Silver::create($silver);
        }

        $silver_value= ZakahHelper::get_silver_value_that_is_applicable_for_zakah(User::first());


        $this->assertEquals(1000, $silver_value);
    }


}
