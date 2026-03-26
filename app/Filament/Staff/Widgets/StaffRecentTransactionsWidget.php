<?php

namespace App\Filament\Staff\Widgets;

use App\Filament\Staff\Resources\Transactions\TransactionResource;
use App\Models\Transaction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class StaffRecentTransactionsWidget extends TableWidget
{
    protected static ?int $sort = 7;

    protected static ?string $heading = '10 dernières transactions';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->check();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Transaction::query()->latest()->limit(10))
            ->paginated(false)
            ->recordUrl(fn ($record) => TransactionResource::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('user.full_name')
                    ->label('Élève')
                    ->formatStateUsing(fn ($record) => $record->user?->name . ' ' . $record->user?->surname),

                TextColumn::make('fee.title')
                    ->label('Frais')
                    ->placeholder('Non renseigné'),

                TextColumn::make('amount')
                    ->label('Montant')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', ' ') . ' F CFA'),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending'   => 'En attente',
                        'completed' => 'Complété',
                        'failed'    => 'Échoué',
                        'refunded'  => 'Remboursé',
                        default     => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'pending'   => 'warning',
                        'completed' => 'success',
                        'failed'    => 'danger',
                        'refunded'  => 'info',
                        default     => 'gray',
                    }),

                TextColumn::make('date')
                    ->label('Date')
                    ->date('d/m/Y'),
            ]);
    }
}
