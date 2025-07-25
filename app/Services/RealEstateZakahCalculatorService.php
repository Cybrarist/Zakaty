<?php

namespace App\Services;

use App\Enums\RealEstateOwnershipMethod;
use App\Enums\RealEstateOwnershipReasonEnum;
use App\Models\Realestate;

class RealEstateZakahCalculatorService
{
    public function get_zakah_applicable_amount(): int|float
    {

        $total_amount = 0;

        $total_amount += $this->get_selling_assets();

        return $total_amount;
    }

    public function get_selling_assets(): float
    {
        return Realestate::whereIn('ownership_reason' , [RealEstateOwnershipReasonEnum::Selling, RealEstateOwnershipReasonEnum::SellingButRentingForNow])
            ->whereIn('method_of_ownership' , RealEstateOwnershipMethod::eligible())
            ->sum('usd_amount');
    }

    public function get_rent_assets(): float
    {

    }
}
