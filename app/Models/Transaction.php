<?php

namespace App\Models;

use App\Casts\MoneyCast;
use App\Enums\TransactionTypeEnum;
use App\Observers\TransactionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(TransactionObserver::class)]
class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;
    protected $fillable=[
        'name',
        'type',
        'amount',
        'currency_id',
        'money_id',
        'user_id',
        'date'
    ];

    protected function casts(): array
    {
        return [
            'amount' => MoneyCast::class,
            'type' => TransactionTypeEnum::class,
            'date'=> 'date',
        ];
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
    public function money(): BelongsTo
    {
        return $this->belongsTo(Money::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
