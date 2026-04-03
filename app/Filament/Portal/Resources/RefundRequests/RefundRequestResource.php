<?php

namespace App\Filament\Portal\Resources\RefundRequests;

use App\Filament\Portal\Resources\RefundRequests\Pages\ListRefundRequests;
use App\Filament\Portal\Resources\RefundRequests\Pages\ViewRefundRequest;
use App\Filament\Portal\Resources\RefundRequests\Schemas\RefundRequestInfolist;
use App\Filament\Portal\Resources\RefundRequests\Tables\RefundRequestsTable;
use App\Models\RefundRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RefundRequestResource extends Resource
{
    protected static ?string $model = RefundRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUturnLeft;

    protected static ?string $navigationLabel = 'Mes Remboursements';

    protected static ?string $modelLabel = 'Demande de remboursement';

    protected static ?string $pluralModelLabel = 'Demandes de remboursement';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id())
            ->with(['transaction.fee']);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->verified === true;
    }

    public static function infolist(Schema $schema): Schema
    {
        return RefundRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RefundRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRefundRequests::route('/'),
            'view'  => ViewRefundRequest::route('/{record}'),
        ];
    }
}