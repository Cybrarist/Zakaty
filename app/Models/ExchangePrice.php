<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangePrice extends Model
{
    /** @use HasFactory<\Database\Factories\ExchangePriceFactory> */
    use HasFactory;

    protected $fillable=[
        "name",
        "value",
    ];

    protected function casts(): array
    {
        return [
            "value" => \App\Casts\MoneyCast::class
        ];
    }
}
