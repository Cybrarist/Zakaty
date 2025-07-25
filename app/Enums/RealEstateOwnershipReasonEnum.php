<?php

namespace App\Enums;

use App\Traits\EnumToArray;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Testing\Fluent\Concerns\Has;

enum RealEstateOwnershipReasonEnum: string implements HasLabel
{
    use EnumToArray;

    case LivingIn="living in";
    case Plowing="plowing";
    case Renting="renting";
    case Selling="selling";
    case SellingButRentingForNow = "selling but renting for now";

    public static function eligible(): array
    {
        return [
            self::SellingButRentingForNow->value,
            self::Selling->value,
            self::Renting->value,
        ];
    }
    public function getLabel(): ?string
    {
        return \Str::headline($this->name) ;
    }

}
