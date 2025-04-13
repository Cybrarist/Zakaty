<?php

namespace App\Filament\Resources\GoldResource\Pages;

use App\Filament\Resources\GoldResource;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;

class ListGold extends ListRecords
{

    protected static string $resource = GoldResource::class;


    protected function getHeaderWidgets(): array
    {
        return [
            GoldResource\Widgets\GoldResourceOverview::make()
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
