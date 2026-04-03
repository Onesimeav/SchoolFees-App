<?php

namespace App\Filament\Staff\Resources\ClassRegistrations\Pages;

use App\Filament\Staff\Resources\ClassRegistrations\ClassRegistrationResource;
use Filament\Resources\Pages\ListRecords;

class ListClassRegistrations extends ListRecords
{
    protected static string $resource = ClassRegistrationResource::class;
}