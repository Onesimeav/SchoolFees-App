<?php

namespace App\Filament\Widgets;

class AdminWelcomeWidget extends \Filament\Widgets\AccountWidget
{
    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';
}
