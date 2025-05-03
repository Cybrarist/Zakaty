<?php

namespace App\Filament\Resources\ZakahPaymentResource\Pages;

use App\Filament\Resources\ZakahPaymentResource;
use App\Models\Currency;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditZakahPayment extends EditRecord
{
    protected static string $resource = ZakahPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['usd_amount'] = $data['amount'] / Currency::find($data['currency_id'])->rate;
        return $data;
    }
}
