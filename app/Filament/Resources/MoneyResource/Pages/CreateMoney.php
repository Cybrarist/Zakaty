<?php

namespace App\Filament\Resources\MoneyResource\Pages;

use App\Filament\Resources\MoneyResource;
use App\Models\Currency;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateMoney extends CreateRecord
{
    protected static string $resource = MoneyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['usd_amount'] = $data['amount'] / Currency::find($data['currency_id'])->rate;
        return $data;
    }
}
