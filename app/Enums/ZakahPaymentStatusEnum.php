<?php

namespace App\Enums;

use App\Traits\EnumToArray;
use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum ZakahPaymentStatusEnum:string implements HasLabel, HasColor
{

    use EnumToArray;
    case Paid = "paid";
    case Pending = "pending";
    case Cancelled = "cancelled";


    public function getLabel(): ?string
    {
        return Str::headline($this->name);
    }


    public function getColor(): string|array|null
    {
        return match ($this->value){
            self::Paid->value => Color::Green,
            self::Pending->value => Color::Amber,
            self::Cancelled->value => Color::Red,
        };
    }
}
