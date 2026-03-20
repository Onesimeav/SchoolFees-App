<?php

namespace App\Filament\Staff\Resources\Grades;

use App\Filament\Staff\Resources\Grades\Pages\CreateGrade;
use App\Filament\Staff\Resources\Grades\Pages\EditGrade;
use App\Filament\Staff\Resources\Grades\Pages\ListGrades;
use App\Filament\Staff\Resources\Grades\Schemas\GradeForm;
use App\Filament\Staff\Resources\Grades\Tables\GradesTable;
use App\Models\Grade;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GradeResource extends Resource
{
    protected static ?string $model = Grade::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $navigationLabel = 'Classes';

    protected static ?string $modelLabel = 'Classe';

    protected static ?string $pluralModelLabel = 'Classes';

    public static function canCreate(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'secretary']) ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'secretary']) ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return GradeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GradesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListGrades::route('/'),
            'create' => CreateGrade::route('/create'),
            'edit'   => EditGrade::route('/{record}/edit'),
        ];
    }
}