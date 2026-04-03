<?php

namespace App\Filament\Staff\Resources\Transactions\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
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
                TextColumn::make('user.full_name')
                    ->label('Étudiant')
                    ->searchable(['name', 'surname'])
                    ->sortable(['name', 'surname'])
                    ->description(fn ($record) => $record->user?->email),
                TextColumn::make('fee.title')
                    ->label('Frais')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->fee?->title),
                TextColumn::make('installment.number')
                    ->label('Versement')
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn ($state) => $state ? 'N°' . $state : '—')
                    ->toggleable()
                    ->placeholder('N/A'),
                TextColumn::make('amount')
                    ->label('Montant')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', ' ') . ' F CFA')
                    ->sortable(),
                TextColumn::make('date')
                    ->label('Date de paiement')
                    ->date('d/m/Y')
                    ->sortable(),
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
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone_number')
                    ->label('Téléphone')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('kkiapay_reference')
                    ->label('Référence')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('—')
                    ->copyable()
                    ->fontFamily('mono'),
                TextColumn::make('created_at')
                    ->label('Créée le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('user')
                    ->label('Étudiant')
                    ->relationship('user', 'email')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('fee')
                    ->label('Frais')
                    ->relationship('fee', 'title')
                    ->searchable()
                    ->preload(),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->recordActions([
                ViewAction::make()
                    ->label('Détails'),
                Action::make('process')
                    ->label('Valider')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Valider la transaction')
                    ->modalDescription('Êtes-vous sûr de vouloir marquer cette transaction comme complétée ?')
                    ->modalSubmitActionLabel('Valider')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        $record->update(['status' => 'completed']);
                        Notification::make()
                            ->title('Transaction validée')
                            ->success()
                            ->send();
                    }),
                Action::make('refund')
                    ->label('Rembourser')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Rembourser la transaction')
                    ->modalDescription('Êtes-vous sûr de vouloir marquer cette transaction comme remboursée ?')
                    ->modalSubmitActionLabel('Rembourser')
                    ->visible(fn ($record) => $record->status === 'completed' && ! auth()->user()?->hasRole('secretary'))
                    ->action(function ($record) {
                        $record->update(['status' => 'refunded']);
                        Notification::make()
                            ->title('Transaction remboursée')
                            ->success()
                            ->send();
                    }),
                EditAction::make()
                    ->label('Modifier')
                    ->visible(fn ($record) => $record->status === 'pending'),
                DeleteAction::make()
                    ->label('Supprimer'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Supprimer la sélection'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
