<?php

namespace App\Filament\Resources\SilverResource\Pages;

use App\Filament\Resources\SilverResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSilvers extends ListRecords
{
    protected static string $resource = SilverResource::class;


    protected function getHeaderWidgets(): array
    {
        return [
          SilverResource\Widgets\SilverResourceOverview::make()
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
