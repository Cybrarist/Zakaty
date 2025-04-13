<?php

namespace App\Filament\Widgets;

use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;

class MoneyAssetsOverview extends BaseWidget
{
    public  string $default_currency_code;
    public  int|float $total_money;
    public  int|float $total_money_in_default_currency;
    public  int|float $total_gold;
    public  int|float $total_gold_in_default_currency;
    public  int|float $total_silver;
    public  int|float $total_silver_in_default_currency;


    protected static ?string $pollingInterval = '600s';

    protected function getStats(): array
    {

        return [
            Stat::make(__('general.money_zakah_assets.total_money_only'),
                $this->default_currency_code .
                Number::format($this->total_money_in_default_currency)
                )->icon('heroicon-s-banknotes')
                ->progress(100)
                ->progressBarColor('primary')
                ->chartColor('primary')
                ->iconPosition('start')
                ->description("$" . Number::format($this->total_money))
                ->iconColor('primary'),

            Stat::make(__('general.money_zakah_assets.total_gold_only'),
                $this->default_currency_code .
                Number::format($this->total_gold_in_default_currency)
                )
                ->icon('heroicon-s-circle-stack')
                ->progress(100)
                ->progressBarColor('warning')
                ->chartColor('warning')
                ->iconPosition('start')
                ->description("$" . Number::format($this->total_gold))
                ->iconColor('warning'),

            Stat::make(__('general.money_zakah_assets.total_silver_only'),
                $this->default_currency_code .
                Number::format($this->total_silver_in_default_currency)
            )->icon('heroicon-s-circle-stack')
                ->progress(100)
                ->progressBarColor('')
                ->chartColor('')
                ->iconPosition('start')
                ->description("$" . Number::format($this->total_silver))
                ->iconColor(''),
        ];
    }
}
