<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum UserRoleEnum: string implements HasLabel
{
    case Admin = 'admin';
    case User = 'user';


    public function getLabel(): ?string
    {
        return Str::headline($this->name);
    }
}
