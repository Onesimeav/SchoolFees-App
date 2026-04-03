<?php

namespace App\Filament\Portal\Resources\RefundRequests\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RefundRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction.fee.title')
                    ->label('Frais concerné')
                    ->placeholder('—')
                    ->searchable(),

                TextColumn::make('transaction.amount')
                    ->label('Montant')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', ' ') . ' F CFA'),

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
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filtersLayout(FiltersLayout::AboveContent)
            ->deferFilters(false)
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending'  => 'En attente',
                        'accepted' => 'Accepté',
                        'refused'  => 'Refusé',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}