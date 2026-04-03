<?php

namespace App\Filament\Staff\Resources\Fees\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Type de frais')
                    ->badge()
                    ->colors([
                        'info' => 'App\\Models\\RegistrationFee',
                        'success' => 'App\\Models\\TuitionFee',
                        'warning' => 'App\\Models\\GeneralFee',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'App\\Models\\RegistrationFee' => 'Inscription',
                        'App\\Models\\TuitionFee' => 'Scolarité',
                        'App\\Models\\GeneralFee' => 'Général',
                        default => $state,
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Intitulé')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                TextColumn::make('classroom')
                    ->label('Classe')
                    ->getStateUsing(fn ($record) => $record->classroom ?? $record->grade?->name)
                    ->toggleable()
                    ->placeholder('—'),
                TextColumn::make('academic_year')
                    ->label('Année scolaire')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('due_before')
                    ->label('Date limite')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('—')
                    ->color(fn ($record) => $record->due_before && $record->due_before->isPast() ? 'danger' : null)
                    ->description(fn ($record) => $record->due_before && $record->due_before->isFuture()
                        ? 'Dans ' . (int) now()->startOfDay()->diffInDays($record->due_before->startOfDay()) . ' j'
                        : null),
                TextColumn::make('total_amount')
                    ->label('Montant')
                    ->money('XOF')
                    ->sortable(),
                TextColumn::make('number_of_installments')
                    ->label('Versements')
                    ->getStateUsing(fn ($record) => $record->type === 'App\\Models\\TuitionFee'
                        ? $record->installments_count
                        : null)
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->placeholder('—')
                    ->tooltip(fn ($record) => $record->type === 'App\\Models\\TuitionFee' ?
                        'Frais de scolarité répartis en versements' :
                        'Non applicable'),
                IconColumn::make('required')
                    ->label('Obligatoire')
                    ->boolean()
                    ->toggleable()
                    ->tooltip(fn ($record) => $record->type === 'App\\Models\\GeneralFee' ?
                        ($record->required ? 'Ce frais est obligatoire' : 'Ce frais est facultatif') :
                        'Non applicable'),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type de frais')
                    ->options([
                        'App\\Models\\RegistrationFee' => 'Frais d\'inscription',
                        'App\\Models\\TuitionFee' => 'Frais de scolarité',
                        'App\\Models\\GeneralFee' => 'Frais généraux',
                    ]),
                SelectFilter::make('academic_year')
                    ->label('Année scolaire')
                    ->options(fn () => \App\Models\Fee::query()
                        ->distinct()
                        ->pluck('academic_year', 'academic_year')
                        ->toArray()
                    ),
                SelectFilter::make('classroom')
                    ->label('Classe')
                    ->options(fn () => \App\Models\Fee::query()
                        ->distinct()
                        ->whereNotNull('classroom')
                        ->pluck('classroom', 'classroom')
                        ->toArray()
                    ),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->deferFilters(false)
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn () => auth()->user()?->hasAnyRole(['admin', 'secretary'])),
                DeleteAction::make()
                    ->visible(fn () => auth()->user()?->hasAnyRole(['admin', 'secretary'])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasAnyRole(['admin', 'secretary'])),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
