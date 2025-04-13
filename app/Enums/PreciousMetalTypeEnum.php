<?php

namespace App\Enums;

use App\Traits\EnumToArray;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum PreciousMetalTypeEnum: string implements HasLabel
{
    use EnumToArray;
    case Bar = "bar";
    case Bracelet = "bracelet";
    case BodyJewelleries = "body jewelleries";
    case Coin = "coin";
    case Earring = "earring";
    case Necklace = "necklace";
    case Ring = "ring";
    case Other = "other";


    public static function jewelleries(): array
    {
        return [
            self::Bracelet,
            self::BodyJewelleries,
            self::Earring,
            self::Necklace,
            self::Ring,
        ];
    }
    public static function non_jewelleries(): array
    {
        return [
            self::Bar,
            self::Coin,
            self::Other,
        ];
    }

    public function getLabel(): ?string
    {
        return Str::headline($this->name);
    }
}
