<?php

namespace App\Enums;

use App\Traits\EnumToArray;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum MoneyTypeEnum: string implements HasLabel
{
    use EnumToArray;
    case Cash = "cash";
    case SavingAccount = "saving account";

    case CurrentAccount = "current account";

    case Other = "other";


    public function getLabel(): ?string
    {
        return Str::headline($this->name);
    }

}
