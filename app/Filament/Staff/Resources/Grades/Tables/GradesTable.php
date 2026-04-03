<?php

namespace App\Filament\Staff\Resources\Grades\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GradesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(60)
                    ->toggleable()
                    ->placeholder('—'),

                TextColumn::make('registration_fees_count')
                    ->label('Frais d\'inscription')
                    ->counts('registrationFees')
                    ->badge()
                    ->color('info'),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->recordActions([
                EditAction::make()
                    ->visible(fn () => auth()->user()?->hasAnyRole(['admin', 'secretary'])),
                DeleteAction::make()
                    ->visible(fn () => auth()->user()?->hasRole('admin')),
            ]);
    }
}