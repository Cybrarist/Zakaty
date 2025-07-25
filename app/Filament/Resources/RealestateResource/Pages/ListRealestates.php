<?php

namespace App\Filament\Resources\RealestateResource\Pages;

use App\Filament\Resources\RealestateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRealestates extends ListRecords
{
    protected static string $resource = RealestateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
