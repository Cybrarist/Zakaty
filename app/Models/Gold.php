<?php

namespace App\Models;

use App\Casts\MoneyCast;
use App\Enums\KaratEnum;
use App\Enums\PreciousMetalColorEnum;
use App\Enums\PreciousMetalTypeEnum;
use App\Enums\WeightEnum;
use App\Observers\GoldObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(GoldObserver::class)]
class Gold extends Model
{
    /** @use HasFactory<\Database\Factories\GoldFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'weight',
        'usd_amount',
        'weight_unit',
        'karat',
        'type',
        'color',
        'images',
        'notes',
        'user_id',
    ];


    protected function casts(): array
    {
        return [
            'images' => 'array',
            'is_jewellery' => 'boolean',
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
         $query->where('is_jewellery', false)
        ->orWhereNotIn('type', PreciousMetalTypeEnum::jewelleries());
    }

    /**
     * Attributes
     */
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
