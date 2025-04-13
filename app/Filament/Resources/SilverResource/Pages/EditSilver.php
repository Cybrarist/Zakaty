<?php

namespace App\Filament\Resources\SilverResource\Pages;

use App\Enums\WeightEnum;
use App\Filament\Resources\SilverResource;
use App\Helpers\CacheHelper;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditSilver extends EditRecord
{
    protected static string $resource = SilverResource::class;


    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['user_id'] = Auth::id();

        $prices = CacheHelper::get_silver_prices();
        $data['usd_amount'] = WeightEnum::get_weight_in_gram($data['weight_unit'], $data['weight']) * $prices['silver_price_'.$data['karat']];

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
