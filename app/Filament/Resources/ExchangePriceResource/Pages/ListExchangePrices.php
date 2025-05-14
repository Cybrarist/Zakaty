<?php

namespace App\Filament\Resources\ExchangePriceResource\Pages;

use App\Filament\Resources\ExchangePriceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExchangePrices extends ListRecords
{
    protected static string $resource = ExchangePriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
