<?php

namespace App\Enums;

use App\Traits\EnumToArray;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum TransactionTypeEnum: string implements HasLabel
{
    use EnumToArray;
    case Expense = "expense";
    case Income = "income";

    public function getLabel(): ?string
    {
        return Str::headline($this->name);
    }

    public static function get_color($value): string
    {
        return match ($value){
            self::Expense => 'danger',
            self::Income => 'success',
        };
    }
}
