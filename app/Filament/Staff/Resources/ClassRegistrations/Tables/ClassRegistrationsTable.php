<?php

namespace App\Filament\Staff\Resources\ClassRegistrations\Tables;

use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClassRegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Élève')
                    ->formatStateUsing(fn ($record) => $record->user->name . ' ' . $record->user->surname)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('user', function (Builder $q) use ($search) {
                            $q->whereRaw('LOWER(CAST(name AS TEXT)) LIKE ?', ['%' . strtolower($search) . '%'])
                                ->orWhereRaw('LOWER(CAST(surname AS TEXT)) LIKE ?', ['%' . strtolower($search) . '%']);
                        });
                    })
                    ->sortable(),

                TextColumn::make('grade.name')
                    ->label('Classe')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

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
                    })
                    ->sortable(),

                TextColumn::make('notes')
                    ->label('Motif')
                    ->limit(50)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('transaction_id')
                    ->label('Paiement')
                    ->boolean()
                    ->getStateUsing(fn ($record) => (bool) $record->transaction_id)
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedXCircle)
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn ($record) => $record->transaction_id ? 'Paiement reçu' : 'Aucun paiement'),

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

                SelectFilter::make('grade_id')
                    ->label('Classe')
                    ->relationship('grade', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),

                Action::make('accept')
                    ->label('Accepter')
                    ->color('success')
                    ->icon(Heroicon::OutlinedCheck)
                    ->visible(fn ($record) => auth()->user()?->hasAnyRole(['admin', 'secretary']) && $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Accepter l\'inscription')
                    ->modalDescription('Confirmer l\'acceptation de cette inscription ?')
                    ->action(fn ($record) => $record->update(['status' => 'accepted'])),

                Action::make('refuse')
                    ->label('Refuser')
                    ->color('danger')
                    ->icon(Heroicon::OutlinedXMark)
                    ->visible(fn ($record) => auth()->user()?->hasAnyRole(['admin', 'secretary']) && $record->status === 'pending')
                    ->form([
                        Textarea::make('notes')
                            ->label('Motif du refus')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(fn ($record, array $data) => $record->update([
                        'status' => 'refused',
                        'notes'  => $data['notes'],
                    ])),
            ]);
    }
}