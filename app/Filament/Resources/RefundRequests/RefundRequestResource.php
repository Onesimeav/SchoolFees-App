<?php

namespace App\Filament\Resources\RefundRequests;

use App\Filament\Resources\RefundRequests\Pages\ListRefundRequests;
use App\Filament\Resources\RefundRequests\Pages\ViewRefundRequest;
use App\Filament\Resources\Transactions\TransactionResource;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Staff\Resources\RefundRequests\RefundRequestResource as StaffRefundRequestResource;
use App\Filament\Staff\Resources\RefundRequests\Tables\RefundRequestsTable;
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

    protected static ?string $navigationLabel = 'Remboursements';

    protected static ?string $modelLabel = 'Demande de remboursement';

    protected static ?string $pluralModelLabel = 'Demandes de remboursement';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user', 'transaction.fee']);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StaffRefundRequestResource::buildInfolists($schema, UserResource::class, TransactionResource::class);
    }

    public static function table(Table $table): Table
    {
        return RefundRequestsTable::configure($table);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
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