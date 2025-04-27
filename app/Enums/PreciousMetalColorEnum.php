<?php

namespace App\Enums;

use App\Traits\EnumToArray;
use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum PreciousMetalColorEnum: string implements  HasLabel
{
    use EnumToArray;
    case RoseGold = "rose gold";
    case Yellow = "yellow";
    case White = "white";

    case Silver = "silver";


    public static function gold()
    {
        return[
            self::RoseGold->value => Str::headline(self::RoseGold->name),
            self::White->value =>Str::headline(self::White->name),
            self::Yellow->value => Str::headline(self::Yellow->name),
        ];
    }

    public static function silver()
    {
        return[
            self::Silver->value => Str::headline(self::Silver->name),
        ];
    }

    public static function get_badge($value)
    {
         return match ($value){
            self::Yellow=>Color::Amber,
            self::White=>Color::Gray,
            self::Silver=>Color::Stone,
            self::RoseGold=>Color::hex('#b76e79'),
            default=>null,
        };
    }
    //
    public function getLabel(): ?string
    {
        return Str::headline($this->name);
    }
}
