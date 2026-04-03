<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\RefundRequests\RefundRequestResource;
use App\Models\RefundRequest;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class AdminRecentRefundsWidget extends TableWidget
{
    protected static ?int $sort = 7;

    protected static ?string $heading = 'Dernières demandes de remboursement';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(RefundRequest::query()->latest()->limit(6))
            ->paginated(false)
            ->recordUrl(fn ($record) => RefundRequestResource::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('user.full_name')
                    ->label('Élève')
                    ->formatStateUsing(fn ($record) => $record->user?->name . ' ' . $record->user?->surname),

                TextColumn::make('transaction.fee.title')
                    ->label('Frais')
                    ->placeholder('Non renseigné'),

                TextColumn::make('transaction.amount')
                    ->label('Montant')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 0, ',', ' ') . ' F CFA' : 'Non renseigné'),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending'  => 'En attente',
                        'accepted' => 'Accepté',
                        'refused'  => 'Refusé',
                        default    => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'pending'  => 'warning',
                        'accepted' => 'success',
                        'refused'  => 'danger',
                        default    => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Soumis le')
                    ->date('d/m/Y'),
            ]);
    }
}
