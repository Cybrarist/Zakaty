<?php

namespace App\Filament\Resources\GoldResource\Pages;

use App\Enums\WeightEnum;
use App\Filament\Resources\GoldResource;
use App\Helpers\CacheHelper;
use App\Models\Currency;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateGold extends CreateRecord
{
    protected static string $resource = GoldResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();

        $prices = CacheHelper::get_gold_prices();
        $data['usd_amount'] = WeightEnum::get_weight_in_gram($data['weight_unit'], $data['weight']) * $prices['gold_price_'.$data['karat']];

        return $data;
    }
}
