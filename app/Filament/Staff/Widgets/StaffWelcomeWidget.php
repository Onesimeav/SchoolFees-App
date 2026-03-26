<?php

namespace App\Filament\Staff\Widgets;

class StaffWelcomeWidget extends \Filament\Widgets\AccountWidget
{
    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';
}
