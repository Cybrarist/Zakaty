<?php

namespace App\Enums;

use App\Traits\EnumToArray;
use Filament\Support\Contracts\HasLabel;

enum RealEstateOwnershipMethod: string implements HasLabel
{
    use EnumToArray;

    case SelfBought = "self bought";
    case Grant = "grant";
    case Inheritance = "inheritance";



    public function getLabel(): ?string
    {
        return \Str::headline($this->name) ;
    }

    public static function eligible(): array
    {
        return [
            self::SelfBought->value,
        ];
    }

}
