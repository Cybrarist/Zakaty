<?php

namespace App\Filament\Widgets;

use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class MoneyAssetsZakahOverview extends BaseWidget
{
    public string $default_currency_code;
    public ?string $money_assets_zakah_date;
    public ?int $days_remaining_to_money_zakah;
    public int|float $total_money_assets_value_default_currency;
    public int|float $total_money_assets_value;
    public int|float $money_zakah_to_pay_default_currency;
    public int|float $money_zakah_to_pay;

    protected static ?string $pollingInterval = '600s';


    protected function getStats(): array
    {


        return [
            Stat::make(__('general.money_zakah_assets.total_value'),
                $this->default_currency_code .
                Number::format($this->total_money_assets_value_default_currency)
            )->icon('heroicon-s-banknotes')
                ->progress(100)
                ->progressBarColor('primary')
                ->chartColor('primary')
                ->iconPosition('start')
                ->description("$" . Number::format($this->total_money_assets_value))
                ->iconColor('primary'),


            Stat::make(__('general.money_zakah_assets.next_zakah_date'), $this->money_assets_zakah_date ?? __('general.money_zakah_assets.no_zakah_date'))
                ->icon('heroicon-s-calendar-days')
                ->progress(100)
                ->progressBarColor('info')
                ->chartColor('info')
                ->iconPosition('start')
                ->description("{$this->days_remaining_to_money_zakah} " . __('general.money_zakah_assets.days_for_payment'))
                ->iconColor('info'),


            Stat::make(__('general.money_zakah_assets.payment'),
                $this->default_currency_code .
                Number::format($this->money_zakah_to_pay_default_currency)
            )->icon('heroicon-s-currency-dollar')
                ->progress(100)
                ->progressBarColor('danger')
                ->chartColor('danger')
                ->iconPosition('start')
                ->description("$" . Number::format($this->money_zakah_to_pay))
                ->iconColor('danger'),


        ];
    }
}
