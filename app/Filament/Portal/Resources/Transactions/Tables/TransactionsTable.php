<?php

namespace App\Filament\Portal\Resources\Transactions\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
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
                    ->label('Fee')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->fee?->academic_year),
                TextColumn::make('installment.number')
                    ->label('Installment')
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn ($state) => $state ? '#' . $state : 'Full Payment')
                    ->placeholder('Full Payment'),
                TextColumn::make('amount')
                    ->label('Amount Paid')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable()
                    ->description(fn ($record) => $record->date->diffForHumans()),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger' => 'failed',
                        'info' => 'refunded',
                    ])
                    ->searchable()
                    ->sortable(),
                TextColumn::make('kkiapay_reference')
                    ->label('Reference')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('N/A')
                    ->copyable()
                    ->copyMessage('Reference copied!')
                    ->copyMessageDuration(1500),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Payment Status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ])
                    ->multiple(),
                SelectFilter::make('fee')
                    ->label('Fee Type')
                    ->relationship('fee', 'title')
                    ->searchable()
                    ->preload(),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->recordActions([
                ViewAction::make()
                    ->label('View Details'),
                DeleteAction::make(),
            ])
            ->defaultSort('date', 'desc')
            ->emptyStateHeading('No payments yet')
            ->emptyStateDescription('Your payment history will appear here once you make a payment.')
            ->emptyStateIcon('heroicon-o-banknotes');
    }
}