<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class TitleWidget extends Widget
{
    public string $title;

    protected static ?string $pollingInterval = '600s';

    protected static string $view = 'filament.widgets.title';
    protected static bool $isDiscovered = false;

}
