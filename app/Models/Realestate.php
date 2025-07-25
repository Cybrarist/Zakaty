<?php

namespace App\Models;

use App\Casts\MoneyCast;
use App\Enums\RealEstateOwnershipMethod;
use App\Enums\RealEstateOwnershipReasonEnum;
use App\Enums\RealEstateRentTypeEnum;
use App\Enums\RealEstateTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Realestate extends Model
{
    /** @use HasFactory<\Database\Factories\RealestateFactory> */
    use HasFactory;

    protected $fillable=[
        "name",
        "type",
        "coordinates",
        "method_of_ownership",
        "ownership_reason",
        "ownership_at",
        "notes",
        "images",
        "documents",
        "owned_at",
        "decided_to_sell_at",
        "sold_at",
        "zakah_at",
        "user_id",
        "currency_id",
        "value",
        "usd_value",
        "rent_amount",
        "rent_usd_amount",
        "rent_type",
        "money_id",
    ];

    protected function casts(): array
    {
        return [
            'type' => RealestateTypeEnum::class,
            'ownership_reason' => RealEstateOwnershipReasonEnum::class,
            'method_of_ownership' => RealEstateOwnershipMethod::class,
            'rent_type' => RealEstateRentTypeEnum::class,
            'value' => MoneyCast::class,
            'usd_value' => MoneyCast::class,
            'coordinates'=> 'array',
            'decided_to_sell_at' => 'date',
            'zakah_at' => 'date',
            'images' => 'array',
            'documents' => 'array',
        ];
    }



    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
