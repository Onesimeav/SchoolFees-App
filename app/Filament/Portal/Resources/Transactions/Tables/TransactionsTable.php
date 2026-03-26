<?php

namespace App\Filament\Portal\Resources\Transactions\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fee.title')
                    ->label('Frais')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn ($record) => $record->fee?->academic_year),
                TextColumn::make('amount')
                    ->label('Montant payé')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', ' ') . ' F CFA')
                    ->sortable()
                    ->weight('semibold')
                    ->color('success'),
                TextColumn::make('date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable()
                    ->description(fn ($record) => $record->date->diffForHumans()),
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
                    })
                    ->sortable(),
                TextColumn::make('kkiapay_reference')
                    ->label('Référence')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('N/A')
                    ->copyable()
                    ->copyMessage('Référence copiée !')
                    ->copyMessageDuration(1500)
                    ->fontFamily('mono')
                    ->color('gray'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending'   => 'En attente',
                        'completed' => 'Complété',
                        'failed'    => 'Échoué',
                        'refunded'  => 'Remboursé',
                    ])
                    ->multiple(),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->deferFilters(false)
            ->recordActions([
                ViewAction::make()
                    ->label('Détails'),
            ])
            ->defaultSort('date', 'desc')
            ->emptyStateHeading('Aucun paiement')
            ->emptyStateDescription("Votre historique de paiements s'affichera ici une fois que vous aurez effectué un paiement.")
            ->emptyStateIcon('heroicon-o-banknotes')
            ->striped();
    }
}