<?php

namespace App\Filament\Resources\ZakahPaymentResource\Pages;

use App\Filament\Resources\ZakahPaymentResource;
use App\Models\Currency;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateZakahPayment extends CreateRecord
{
    protected static string $resource = ZakahPaymentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['usd_amount'] = $data['amount'] / Currency::find($data['currency_id'])->rate;
        return $data;
    }
}
