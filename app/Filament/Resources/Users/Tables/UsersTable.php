<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Nom complet')
                    ->searchable(['name', 'surname'])
                    ->sortable(['name', 'surname'])
                    ->formatStateUsing(fn ($record) => $record->full_name),
                TextColumn::make('email')
                    ->label('Adresse email')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('phone_number')
                    ->label('Téléphone')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('roles.name')
                    ->label('Rôles')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'admin' => 'Administrateur',
                        'accountant' => 'Comptable',
                        'secretary' => 'Secrétaire',
                        'employee' => 'Employé',
                        'parent_student' => 'Parent / Élève',
                        default => $state,
                    })
                    ->colors([
                        'danger' => 'admin',
                        'warning' => 'accountant',
                        'info' => 'secretary',
                        'success' => 'parent_student',
                        'gray' => 'employee',
                    ])
                    ->searchable(),
                IconColumn::make('verified')
                    ->label('Vérifié')
                    ->boolean()
                    ->toggleable(),
                TextColumn::make('matricule')
                    ->label('Matricule')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('N/A'),
                TextColumn::make('classroom')
                    ->label('Classe')
                    ->toggleable()
                    ->placeholder('N/A'),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->label('Rôle')
                    ->options([
                        'admin'          => 'Administrateur',
                        'accountant'     => 'Comptable',
                        'secretary'      => 'Secrétaire',
                        'employee'       => 'Employé',
                        'parent_student' => 'Parent / Élève',
                    ])
                    ->multiple()
                    ->query(fn (Builder $query, array $data): Builder =>
                        filled($data['values'])
                            ? $query->whereHas('roles', fn ($q) => $q->whereIn('name', $data['values']))
                            : $query
                    ),
                TernaryFilter::make('verified')
                    ->label('Email vérifié')
                    ->placeholder('Tous les utilisateurs')
                    ->trueLabel('Utilisateurs vérifiés')
                    ->falseLabel('Utilisateurs non vérifiés'),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->deferFilters(false)
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
