<?php

namespace App\Models;

use App\Casts\MoneyCast;
use App\Enums\MoneyTypeEnum;
use App\Models\Scopes\OwnRecordScope;
use App\Observers\MoneyObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(MoneyObserver::class)]
#[ScopedBy(OwnRecordScope::class)]
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
            'amount' => MoneyCast::class,
            'usd_amount' => MoneyCast::class,
            'type' => MoneyTypeEnum::class,
        ];
    }


    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
