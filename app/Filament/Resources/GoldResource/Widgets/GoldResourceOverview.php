<?php

namespace App\Filament\Resources\GoldResource\Widgets;

use App\Helpers\CacheHelper;
use App\Helpers\ZakahHelper;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;

class GoldResourceOverview extends BaseWidget
{

    protected $listeners=['refresh_gold_widgets'=>'$refresh'];


    protected static ?string $pollingInterval='60s';

    protected function getStats(): array
    {
        $money_assets_zakah_date =Auth::user()->next_money_zakah_date;
        $nisab_today= CacheHelper::get_nisab();

        $gold_only_value_in_default_currency = Auth::user()->total_gold_usd * Auth::user()->currency->rate;

        $remaining_for_money_to_reach_nisab= $nisab_today - Auth::user()->total_money_assets;
        $remaining_for_money_to_reach_nisab_in_default_currency = $remaining_for_money_to_reach_nisab * Auth::user()->currency->rate;

        $amount_to_pay_for_gold_only = ZakahHelper::get_gold_value_that_is_applicable_for_zakah(Auth::user()) / 40 ;
        $amount_to_pay_for_gold_only_in_local_currency =$amount_to_pay_for_gold_only * Auth::user()->currency->rate;

        $progress = ($remaining_for_money_to_reach_nisab <= 0) ? 100 : Auth::user()->total_money_assets * 100 / $nisab_today;
        $days_remaining_to_zakah = ($money_assets_zakah_date) ? today()->diff($money_assets_zakah_date)->totalDays : 0;


        return [
            Stat::make('Gold Only Total Value',
                    Auth::user()->currency->currency_symbol .
                    Number::format($gold_only_value_in_default_currency)
                )->icon('heroicon-s-circle-stack')
                ->progress($progress)
                ->progressBarColor('primary')
                ->chartColor('primary')
                ->iconPosition('start')
                ->description(
                    ($money_assets_zakah_date) ? 'Reached Nisab (with Money & Silver) ' :
                        Auth::user()->currency->currency_symbol .
                        Number::format($remaining_for_money_to_reach_nisab_in_default_currency) . ' Remaining to Reach Nisab'
                )
                ->iconColor('primary'),

            Stat::make("Next Zakah Date", $money_assets_zakah_date?->toDateString() ?? 'No Zakah Date')
                ->icon('heroicon-s-calendar-days')
                ->progress($days_remaining_to_zakah)
                ->progressBarColor('info')
                ->chartColor('info')
                ->iconPosition('start')
                ->description("{$days_remaining_to_zakah} Days Until Payment")
                ->iconColor('info'),


            Stat::make("Amount To Pay For Gold Only",
                    Auth::user()->currency->currency_symbol .
                    Number::format($amount_to_pay_for_gold_only_in_local_currency)
                )->icon('heroicon-s-currency-dollar')
                ->progress(100)
                ->progressBarColor('danger')
                ->chartColor('danger')
                ->iconPosition('start')
                ->description("$" . Number::format($amount_to_pay_for_gold_only))
                ->iconColor('danger'),


        ];
    }
}
