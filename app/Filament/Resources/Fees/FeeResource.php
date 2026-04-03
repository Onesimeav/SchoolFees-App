<?php

namespace App\Filament\Resources\Fees;

use App\Filament\Resources\Fees\Pages\CreateFee;
use App\Filament\Resources\Fees\Pages\EditFee;
use App\Filament\Resources\Fees\Pages\ListFees;
use App\Filament\Resources\Fees\Pages\ViewFee;
use App\Filament\Staff\Resources\Fees\RelationManagers\InstallmentsRelationManager;
use App\Filament\Staff\Resources\Fees\Schemas\FeeForm;
use App\Filament\Staff\Resources\Fees\Schemas\FeeInfolist;
use App\Filament\Staff\Resources\Fees\Tables\FeesTable;
use App\Models\Fee;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FeeResource extends Resource
{
    protected static ?string $model = Fee::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Frais';

    protected static ?string $modelLabel = 'Frais';

    protected static ?string $pluralModelLabel = 'Frais';

    public static function form(Schema $schema): Schema
    {
        return FeeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FeeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FeesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount('installments');
    }

    public static function getRelations(): array
    {
        return [
            InstallmentsRelationManager::class,
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListFees::route('/'),
            'create' => CreateFee::route('/create'),
            'view'   => ViewFee::route('/{record}'),
            'edit'   => EditFee::route('/{record}/edit'),
        ];
    }
}