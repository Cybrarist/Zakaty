<?php

namespace App\Helpers;

use App\Enums\PreciousMetalTypeEnum;
use App\Models\Gold;
use App\Models\Money;
use App\Models\Silver;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ZakahHelper
{
    public static function  get_gold_value_that_is_applicable_for_zakah(User $user)
    {
        if (! $user->settings["consider_jeweleries_in_zakah"]) {
            return $user->gold()->nonJewellery()->sum('usd_amount') / 1000;
        }

        return $user->gold()->sum('usd_amount') / 1000;
    }

    public static function  get_silver_value_that_is_applicable_for_zakah(User $user)
    {
        if (! $user->settings["consider_jeweleries_in_zakah"]) {
            return $user->silver()->nonJewellery()->sum('usd_amount') / 1000;
        }

        return $user->silver()->sum('usd_amount') / 1000;
    }



    // update the user information for the zakah updated / created
    public static function update_money_zakah_data_for_user(User $user): void
    {
        $user->update([
            'total_money_usd' => $user->money()->sum('usd_amount') / 1000,
        ]);
    }

    public static function update_gold_zakah_data_for_user(User $user): void
    {
        // get the current exchange prices
        $prices = CacheHelper::get_gold_prices();
        $total_gold_value = 0;

        $user_golds = Gold::where('user_id', $user->id);

        foreach ($user_golds->get() as $gold) {
            $total_gold_value += $gold->weight_in_grams * $prices['gold_price_'.$gold->karat->value];
        }

        $user->update([
            'total_gold_usd' => $total_gold_value,
        ]);

    }

    public static function update_silver_zakah_data_for_user(User $user): void
    {
        // get the current exchange prices
        $prices = CacheHelper::get_silver_prices();
        $total_silver_value = 0;

        $user_silvers = Silver::where('user_id', $user->id);

        foreach ($user_silvers->get() as $silver) {
            $total_silver_value += $silver->weight_in_grams * $prices['silver_price_'.$silver->karat->value];
        }

        $user->update([
            'total_silver_usd' => $total_silver_value,
        ]);

    }

    public static function calculate_total_value_for_gold_in_usd(): float|int
    {
        // get the current exchange prices
        $prices = CacheHelper::get_gold_prices();
        $total_gold_value = 0;
        foreach (Auth::user()->golds ?? [] as $gold) {
            $total_gold_value = $gold->weight_in_grams * $prices['gold_price_'.$gold->karat->value];
        }

        return $total_gold_value;
    }

    public static function calculate_total_value_for_silver_in_usd(): float|int
    {
        // get the current exchange prices
        $prices = CacheHelper::get_silver_prices();
        $total_silver_value = 0;
        foreach (Auth::user()->silvers ?? [] as $silver) {
            $total_silver_value = $silver->weight_in_grams * $prices['silver_price_'.$silver->karat->value];
        }

        return $total_silver_value;
    }

    /**
     * - Check if the user has a next date, if he does, and the date is in future then
     * only calculate the money needed to be paid
     *
     * - if he doesn't have a date, then set the date up with the money
     *
     * - if money got less than nisab then send email to user and let him take action
     * to reset the date or not.
     */
    public static function set_amount_and_date_for_zakah(User $user): void
    {
        $minimum_nisab = CacheHelper::get_nisab();

        $gold_to_be_considered_for_zakah= $user->total_gold_usd;
        $silver_to_be_considered_for_zakah= $user->total_silver_usd;

        if (! $user->settings["consider_jeweleries_in_zakah"]) {
            $gold_to_be_considered_for_zakah = $user->gold()->nonJewellery()->sum('usd_amount') / 1000;
            $silver_to_be_considered_for_zakah =  $user->silver()->nonJewellery()->sum('usd_amount') / 1000;
        }

        $total_money = $user->total_money_usd +
            $gold_to_be_considered_for_zakah +
            $silver_to_be_considered_for_zakah;


        DB::transaction(function () use ($minimum_nisab, $total_money, $user) {

            if ($total_money >= $minimum_nisab) {

                if ((! $user->next_money_zakah_date || $user->next_money_zakah_date?->isPast()) && ! $user->next_money_zakah_date?->isToday()) {
                    $user->next_money_zakah_date = today()->addYear();
                }

                $user->total_pay_money = $total_money / 40;

                $user->save();

            } elseif ($user->next_money_zakah_date &&
                $user->next_money_zakah_date?->isFuture()) {

                $user->update([
                    'next_money_zakah_date' => null,
                    'total_pay_money' => 0,
                ]);
            }
        });
    }
}
