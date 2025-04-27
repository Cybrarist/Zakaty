<?php

namespace App\Models;

use App\Casts\MoneyCast;
use App\Enums\KaratEnum;
use App\Enums\PreciousMetalColorEnum;
use App\Enums\PreciousMetalTypeEnum;
use App\Enums\WeightEnum;
use App\Models\Scopes\SilverScope;
use App\Observers\SilverObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(SilverObserver::class)]
#[ScopedBy(SilverScope::class)]
class Silver extends Model
{
    /** @use HasFactory<\Database\Factories\SilverFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'weight',
        'usd_amount',
        'weight_unit',
        'karat',
        "type",
        'color',
        'images',
        'notes',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'weight' => MoneyCast::class,
            'usd_amount' => MoneyCast::class,
            'weight_unit' => WeightEnum::class,
            'karat' => KaratEnum::class,
            'type' => PreciousMetalTypeEnum::class,
            'color' => PreciousMetalColorEnum::class,
        ];
    }

    /**
     * Scopes
     */
    public function scopeNonJewellery(Builder $query): void
    {
        $query->whereNotIn('type', PreciousMetalTypeEnum::jewelleries());
    }

    // Attributes
    protected function getWeightInGramsAttribute(): float|int
    {
        return WeightEnum::get_weight_in_gram($this->weight_unit, $this->weight);
    }

    // relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
