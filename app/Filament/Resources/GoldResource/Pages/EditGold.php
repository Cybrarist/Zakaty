<?php

namespace App\Filament\Resources\GoldResource\Pages;

use App\Enums\WeightEnum;
use App\Filament\Resources\GoldResource;
use App\Helpers\CacheHelper;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditGold extends EditRecord
{
    protected static string $resource = GoldResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['user_id'] = Auth::id();

        $prices = CacheHelper::get_gold_prices();
        $data['usd_amount'] = WeightEnum::get_weight_in_gram($data['weight_unit'], $data['weight']) * $prices['gold_price_'.$data['karat']];

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
