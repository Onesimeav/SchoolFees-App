<?php

namespace App\Filament\Resources\ClassRegistrations\Pages;

use App\Filament\Resources\ClassRegistrations\ClassRegistrationResource;
use Filament\Resources\Pages\ListRecords;

class ListClassRegistrations extends ListRecords
{
    protected static string $resource = ClassRegistrationResource::class;
}