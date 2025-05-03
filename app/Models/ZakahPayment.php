<?php

namespace App\Models;

use App\Casts\MoneyCast;
use App\Enums\ZakahPaymentMethodEnum;
use App\Enums\ZakahPaymentStatusEnum;
use App\Enums\ZakahPaymentTypeEnum;
use App\Models\Scopes\OwnRecordScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy(OwnRecordScope::class)]
class ZakahPayment extends Model
{
    /** @use HasFactory<\Database\Factories\ZakahPaymentFactory> */
    use HasFactory;

    protected $fillable =[
        'name',
        'amount',
        'usd_amount',
        'type',
        'status',
        'payment_method',
        'paid_at',
        'user_id',
        'currency_id',
        'notes',
        'images',
    ];

    protected function casts(): array
    {
        return [
            'amount' => MoneyCast::class,
            'usd_amount' => MoneyCast::class,
            'type' => ZakahPaymentTypeEnum::class,
            'status' => ZakahPaymentStatusEnum::class,
            'payment_method' => ZakahPaymentMethodEnum::class,
            'images' => 'array',
            'paid_at' => 'date',
        ];
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
