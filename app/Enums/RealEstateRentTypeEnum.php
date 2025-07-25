<?php

namespace App\Enums;

use App\Traits\EnumToArray;
use Filament\Support\Contracts\HasLabel;

enum RealEstateRentTypeEnum: string implements HasLabel
{
    use EnumToArray;

    case Yearly = "yearly";
    case SemiYearly = "semi yearly";
    case Quarterly = "quarterly";
    case Monthly = "monthly";
    case Weekly = "weekly";
    case Daily = "daily";
    case Hourly = "hourly";
    case Other = "other";


    public function getLabel(): ?string
    {
        return $this->name;
    }
}
