<?php

namespace App\Enums;

use App\Traits\EnumToArray;
use Illuminate\Support\Str;

enum WeightEnum: string
{
    use EnumToArray;
    case Gram = "g";
    case KiloGram = "kg";

    case Ounce = "oz";

    case Pound = "lb";


    public function getLabel(): ?string
    {
        return Str::headline($this->name);
    }

    public static function get_weight_in_gram($value, $weight): float|int
    {

        return match ($value) {
            WeightEnum::KiloGram => $weight * 1000,
            WeightEnum::Ounce => $weight * 28.35,
            WeightEnum::Pound => $weight * 453.6,
            default => $weight
        };
    }
}
