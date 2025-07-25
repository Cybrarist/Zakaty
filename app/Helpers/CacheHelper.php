<?php

namespace App\Helpers;

use App\Models\ExchangePrice;
use App\Models\Money;
use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    public static function get_gold_prices()
    {
        return Cache::remember("gold_prices", 86400, function () {
            return ExchangePrice::where("name", "LIKE", "gold_%")->pluck("value", "name");
        });
    }

    public static function get_silver_prices()
    {
        return Cache::remember("silver_prices", 86400, function () {
            return ExchangePrice::where("name", "LIKE", "silver_%")->pluck("value", "name");
        });
    }

    public static function get_precious_metal_price()
    {
        return Cache::remember("precious_metal_prices", 86400, function () {
            return ExchangePrice::whereIn('name', [
                'gold_price_24',
                'silver_price_24', ])
                ->pluck("value", "name")
                ->toArray();
        });
    }
    public static function get_nisab()
    {
        return Cache::remember('today_minimum_nisab', 86400, function () {
            // minimum nisab for money is the least between 85g of gold and 595g of silver.
            $price_for_gold_24_and_silver_24 = ExchangePrice::whereIn('name', [
                'gold_price_24',
                'silver_price_24', ])
                ->pluck("value", "name")
                ->toArray();

            return min($price_for_gold_24_and_silver_24['gold_price_24'] * 85,
                $price_for_gold_24_and_silver_24['silver_price_24'] * 595);

        });
    }


    public static function clear_cache_for_zakah_requirements(): void
    {
        Cache::forget("today_minimum_nisab");
        Cache::forget("precious_metal_price");
        Cache::forget("silver_prices");
        Cache::forget("gold_prices");
    }


    public static function get_currencies_for_user_money_accounts(int $user_id)
    {
        return Cache::remember("currencies_for_money_{$user_id}", 86400, function () use ($user_id) {
            return Money::withoutGlobalScopes()
                ->where('user_id', $user_id)
                ->distinct('currency_id')
                ->pluck('currency_id')
                ->toArray();
        });
    }
}
