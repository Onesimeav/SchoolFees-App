<?php

namespace App\Filament\Portal\Resources\Transactions;

use App\Filament\Portal\Resources\Transactions\Pages\ListTransactions;
use App\Filament\Portal\Resources\Transactions\Pages\ViewTransaction;
use App\Filament\Portal\Resources\Transactions\Schemas\TransactionInfolist;
use App\Filament\Portal\Resources\Transactions\Tables\TransactionsTable;
use App\Models\Transaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Mes Transactions';

    protected static ?string $modelLabel = 'Transaction';

    protected static ?string $pluralModelLabel = 'Transactions';

    public static function getEloquentQuery(): Builder
    {
        // Students can only see their own transactions
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id())
            ->with(['fee.grade', 'classRegistration.grade']);
    }

    public static function canCreate(): bool
    {
        // Students cannot create transactions
        return false;
    }

    public static function canEdit($record): bool
    {
        // Students cannot edit transactions
        return false;
    }

    public static function canDelete($record): bool
    {
        // Students cannot delete transactions
        return false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->verified === true;
    }

    public static function infolist(Schema $schema): Schema
    {
        return TransactionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransactionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransactions::route('/'),
            'view' => ViewTransaction::route('/{record}'),
        ];
    }
}
