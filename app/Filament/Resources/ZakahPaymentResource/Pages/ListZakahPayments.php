<?php

namespace App\Filament\Resources\ZakahPaymentResource\Pages;

use App\Filament\Resources\ZakahPaymentResource;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;

class ListZakahPayments extends ListRecords
{

    protected static string $resource = ZakahPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }


    protected function getFooterWidgets(): array
    {
        return [
            ZakahPaymentResource\Widgets\ZakahPaymentChart::class,
            ZakahPaymentResource\Widgets\ZakahPaymentDonutChart::class,
        ];
    }
}
