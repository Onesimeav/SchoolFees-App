<?php

namespace App\Filament\Staff\Resources\Grades\Pages;

use App\Filament\Staff\Resources\Grades\GradeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGrade extends EditRecord
{
    protected static string $resource = GradeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => auth()->user()?->hasRole('admin')),
        ];
    }
}