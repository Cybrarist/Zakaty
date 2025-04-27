<?php

namespace App\Models;

use App\Enums\MoneyTypeEnum;
use App\Models\Scopes\MoneyScope;
use App\Observers\MoneyObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(MoneyObserver::class)]
#[ScopedBy(MoneyScope::class)]
class Money extends Model
{
    /** @use HasFactory<\Database\Factories\MoneyFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'amount',
        'usd_amount',
        'type',
        'images',
        'notes',
        'user_id',
        'currency_id',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'amount' => \App\Casts\MoneyCast::class,
            'usd_amount' => \App\Casts\MoneyCast::class,
            'type' => MoneyTypeEnum::class,
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
