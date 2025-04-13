<?php

namespace App\Filament\Resources\MoneyResource\Pages;

use App\Filament\Resources\MoneyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMoney extends ListRecords
{
    protected static string $resource = MoneyResource::class;


    protected function getHeaderWidgets(): array
    {
        return [
            MoneyResource\Widgets\MoneyResourceOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
