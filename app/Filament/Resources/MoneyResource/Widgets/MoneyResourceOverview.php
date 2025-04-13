<?php

namespace App\Filament\Resources\MoneyResource\Widgets;

use App\Helpers\CacheHelper;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;

class MoneyResourceOverview extends BaseWidget
{
    protected $listeners=['refresh_money_widgets'=>'$refresh'];

    protected static ?string $pollingInterval='600s';


    protected function getStats(): array
    {
        $money_assets_zakah_date =Auth::user()->next_money_zakah_date;
        $nisab_today= CacheHelper::get_nisab();

        $money_only_value_in_default_currency = Auth::user()->total_money_usd * Auth::user()->currency->rate;

        $remaining_for_money_to_reach_nisab= $nisab_today - Auth::user()->total_money_assets;
        $remaining_for_money_to_reach_nisab_in_default_currency = $remaining_for_money_to_reach_nisab * Auth::user()->currency->rate;

        $amount_to_pay_for_money_only = ($money_assets_zakah_date) ? Auth::user()->total_money_usd /40 : 0;
        $amount_to_pay_for_money_only_in_local_currency =$amount_to_pay_for_money_only * Auth::user()->currency->rate;

        $progress = ($remaining_for_money_to_reach_nisab <= 0) ? 100 : Auth::user()->total_money_assets * 100 / $nisab_today;
        $days_remaining_to_zakah = ($money_assets_zakah_date) ? today()->diff($money_assets_zakah_date)->totalDays : 0;

        return [
            Stat::make(__('general.money_zakah_assets.total_money_only'),
                Auth::user()->currency->currency_symbol .
                    Number::format($money_only_value_in_default_currency)
                )->icon('heroicon-s-banknotes')
                ->progress($progress)
                ->progressBarColor('primary')
                ->chartColor('primary')
                ->iconPosition('start')
                ->description(
                    ($money_assets_zakah_date && Auth::user()->total_pay_money) ? " " . __('general.reached_with_gold_and_silver') :
                        Auth::user()->currency->currency_symbol .
                        Number::format($remaining_for_money_to_reach_nisab_in_default_currency) . " " . __('general.remaining_amount_for_nisab')
                )
                ->iconColor('primary'),

            Stat::make(__('general.money_zakah_assets.next_zakah_date'), $money_assets_zakah_date?->toDateString() ?? __('general.money_zakah_assets.no_zakah_date') )
                ->icon('heroicon-s-calendar-days')
                ->progress($days_remaining_to_zakah)
                ->progressBarColor('info')
                ->chartColor('info')
                ->iconPosition('start')
                ->description("{$days_remaining_to_zakah} " . __('general.money_zakah_assets.days_for_payment'))
                ->iconColor('info'),

            Stat::make(__("general.money.payment"),
                    Auth::user()->currency->currency_symbol .
                    Number::format($amount_to_pay_for_money_only_in_local_currency)
                )->icon('heroicon-s-currency-dollar')
                ->progress(100)
                ->progressBarColor('danger')
                ->chartColor('danger')
                ->iconPosition('start')
                ->description("$" . Number::format($amount_to_pay_for_money_only))
                ->iconColor('danger'),
//

        ];
    }
}
