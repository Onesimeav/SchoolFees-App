<?php

namespace App\Filament\Staff\Resources\Grades\Pages;

use App\Filament\Staff\Resources\Grades\GradeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGrade extends CreateRecord
{
    protected static string $resource = GradeResource::class;
}