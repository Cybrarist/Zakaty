<?php

namespace App\Filament\Widgets;

use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class PricesToday extends BaseWidget
{
    public  string $default_currency_code;
    public  int|float $nisab_today_in_default_currency;
    public  int|float $nisab_today;
    public  int|float $gold_price_in_default_currency;
    public  int|float $gold_price;
    public  int|float $silver_price_in_default_currency;
    public  int|float $silver_price;


    protected static ?string $pollingInterval = '600s';

    protected function getStats(): array
    {

        return [
            Stat::make(__('general.nisab.today'), $this->default_currency_code .
                Number::format($this->nisab_today_in_default_currency)
                )->icon('heroicon-s-banknotes')
                ->progress(100)
                ->progressBarColor('info')
                ->chartColor('info')
                ->iconPosition('start')
                ->description("$" . Number::format($this->nisab_today) )
                ->iconColor('info'),

            Stat::make(__('general.nisab.gold'),$this->default_currency_code .
                Number::format($this->gold_price_in_default_currency)
                )->icon('heroicon-s-circle-stack')
                ->progress(100)
                ->progressBarColor('warning')
                ->chartColor('warning')
                ->iconPosition('start')
                ->description("$" . Number::format($this->gold_price) )
                ->iconColor('warning'),

            Stat::make(__('general.nisab.silver'),
                $this->default_currency_code .
                Number::format($this->silver_price_in_default_currency)
                )->icon('heroicon-s-circle-stack')
                ->progress(100)
                ->progressBarColor('')
                ->chartColor('')
                ->iconPosition('start')
                ->description("$" . Number::format($this->silver_price))
                ->iconColor(''),
        ];
    }
}
