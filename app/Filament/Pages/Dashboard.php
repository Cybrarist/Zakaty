<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\MoneyAssetsOverview;
use App\Filament\Widgets\MoneyAssetsZakahOverview;
use App\Filament\Widgets\TitleWidget;
use App\Filament\Widgets\PricesToday;
use App\Helpers\CacheHelper;
use App\Helpers\ZakahHelper;
use Filament\Actions\LocaleSwitcher;
use Illuminate\Support\Facades\Auth;

class Dashboard extends \Filament\Pages\Dashboard
{


    public function getColumns(): int|string|array
    {
        return 2;
    }

    protected function getHeaderWidgets(): array
    {
        //Prepare for data
        $money_assets_zakah_date =Auth::user()->next_money_zakah_date;
        $default_currency= Auth::user()->currency;
        $nisab_today =  CacheHelper::get_nisab();
        $prices = CacheHelper::get_precious_metal_price();
        $gold_price= $prices['gold_price_24'];
        $silver_price= $prices['silver_price_24'];

        //data in user's default currency
        $gold_price_in_default_currency = $gold_price * $default_currency->rate;
        $silver_price_in_default_currency = $silver_price * $default_currency->rate;


        //Zakah monetary values in $
        $money_zakah_to_pay = Auth::user()->total_money_usd +
            ZakahHelper::get_gold_value_that_is_applicable_for_zakah(Auth::user()) +
            ZakahHelper::get_silver_value_that_is_applicable_for_zakah(Auth::user());


        $money_zakah_to_pay /=40;

        $nisab_today_in_default_currency = $nisab_today * $default_currency->rate;

        $days_remaining_to_money_zakah = ($money_assets_zakah_date) ? today()->diff($money_assets_zakah_date)->totalDays : 0;

        //Zakah Monetary values in the user's default currency
        $total_money_assets_value_default_currency = Auth::user()->total_money_assets * $default_currency->rate;
        $total_money_in_default_currency = Auth::user()->total_money_usd * $default_currency->rate;
        $total_gold_in_default_currency = Auth::user()->total_gold_usd * $default_currency->rate;
        $total_silver_in_default_currency =Auth::user()->total_silver_usd * $default_currency->rate;

        $money_zakah_to_pay_default_currency = $money_zakah_to_pay * $default_currency->rate;

        return [
            PricesToday::make([
                'default_currency_code'=>Auth::user()->currency->currency_symbol,
                'nisab_today_in_default_currency'=>$nisab_today_in_default_currency,
                'nisab_today'=>$nisab_today,
                'gold_price_in_default_currency'=>$gold_price_in_default_currency,
                'gold_price'=>$gold_price,
                'silver_price_in_default_currency'=>$silver_price_in_default_currency,
                'silver_price'=>$silver_price,
            ]),
            TitleWidget::make([
                'title' => __('general.money_zakah_assets.title')
            ]),
            MoneyAssetsOverview::make([
                'default_currency_code'=>Auth::user()->currency->currency_symbol,
                'total_money'=>Auth::user()->total_money_usd,
                'total_money_in_default_currency'=>$total_money_in_default_currency,
                'total_gold'=>Auth::user()->total_gold_usd,
                'total_gold_in_default_currency'=>$total_gold_in_default_currency,
                'total_silver'=>Auth::user()->total_silver_usd,
                'total_silver_in_default_currency'=>$total_silver_in_default_currency,
            ]),
            TitleWidget::make([
                'title' => __('general.money_zakah_assets.upcoming_title')
            ]),
            MoneyAssetsZakahOverview::make([
                'default_currency_code'=>Auth::user()->currency->currency_symbol,
                'total_money_assets_value_default_currency'=>$total_money_assets_value_default_currency,
                'total_money_assets_value'=>Auth::user()->total_money_assets,
                'money_assets_zakah_date'=>$money_assets_zakah_date?->toDateString(),
                'days_remaining_to_money_zakah'=>$days_remaining_to_money_zakah,
                'money_zakah_to_pay_default_currency'=>$money_zakah_to_pay_default_currency,
                'money_zakah_to_pay'=>$money_zakah_to_pay,
            ])
        ];
    }

}
