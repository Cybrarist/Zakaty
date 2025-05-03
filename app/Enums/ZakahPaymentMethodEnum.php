<?php

namespace App\Enums;

use App\Traits\EnumToArray;
use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum ZakahPaymentMethodEnum:string implements HasLabel, HasColor
{
    use EnumToArray;
    case Cash = "cash";
    case CreditCard = "credit_card";
    case DebitCard = "debit_card";
    case Crypto = "crypto";


    public function money_zakah_payment_methods(): array
    {
        return [
            self::Cash->value => self::Cash,
            self::CreditCard->value => self::CreditCard,
            self::DebitCard->value => self::DebitCard,
            self::Crypto->value => self::Crypto,
        ];
    }


    public function getLabel(): ?string
    {
        return Str::headline($this->name);
    }

    public function getColor(): string|array|null
    {
        return match($this->value) {
            self::Cash->value => Color::Green,
            self::CreditCard->value => Color::Blue,
            self::DebitCard->value => Color::Red,
            self::Crypto->value => Color::Gray,

        };
    }
}
