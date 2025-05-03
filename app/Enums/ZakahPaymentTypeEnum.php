<?php

namespace App\Enums;

use App\Traits\EnumToArray;
use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;
use Spatie\Color\Convert;

enum ZakahPaymentTypeEnum:string implements HasLabel, HasColor
{
    use EnumToArray;

    case Money = "money";
    case Animal = "animal";
    case Crops = "crop";

    public function getLabel(): ?string
    {
        return Str::headline($this->name);
    }

    public function getColor(): string|array|null
    {
        return match ($this->value){
            self::Money->value => Color::Green,
            self::Animal->value => Color::Blue,
            self::Crops->value => Color::Red,
        };
    }

    public static  function get_colors(): array
    {
        return ['#16A34A', '#3b82f6', '#dc2626'];

    }
}
