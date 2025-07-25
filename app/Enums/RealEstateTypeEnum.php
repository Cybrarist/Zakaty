<?php

namespace App\Enums;

use App\Traits\EnumToArray;
use Filament\Support\Contracts\HasLabel;

enum RealEstateTypeEnum: string
{
    use EnumToArray;
    case Apartment="apartment";
    case Building="building";
    case Land="land";
    case Townhouse="townhouse";
    case Office= "office";
    case Villa="villa";
    case Warehouse="warehouse";


    public function getLabel(): ?string
    {
        return $this->name;
    }
}
