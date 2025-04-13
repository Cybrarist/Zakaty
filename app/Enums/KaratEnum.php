<?php

namespace App\Enums;


use App\Traits\EnumToArray;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum KaratEnum: int implements HasLabel
{
    use EnumToArray;

    case Karat10=10;
    case Karat14=14;
    case Karat16=16;
    case Karat18=18;
    case Karat20=20;
    case Karat21=21;
    case Karat22=22;
    case Karat24=24;




    public function getLabel(): ?string
    {
        return $this->value . ' Karats';
    }
}
