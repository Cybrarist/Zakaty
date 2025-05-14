<?php

namespace App\Filament\Resources\ExchangePriceResource\Pages;

use App\Filament\Resources\ExchangePriceResource;
use App\Helpers\CacheHelper;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;

class EditExchangePrice extends EditRecord
{
    protected static string $resource = ExchangePriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        CacheHelper::clear_cache_for_zakah_requirements();
    }
}
