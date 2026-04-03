<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconPosition;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations générales')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Prénom'),
                        TextEntry::make('surname')
                            ->label('Nom'),
                        TextEntry::make('email')
                            ->label('Adresse email')
                            ->icon('heroicon-s-clipboard-document')
                            ->iconPosition(IconPosition::After)
                            ->copyable(),
                        TextEntry::make('phone_number')
                            ->label('Numéro de téléphone')
                            ->placeholder('Non renseigné'),
                        IconEntry::make('verified')
                            ->label('Email vérifié')
                            ->boolean(),
                        TextEntry::make('created_at')
                            ->label('Créé le')
                            ->dateTime(),
                    ])
                    ->columns(2),

                Section::make('Rôles')
                    ->schema([
                        TextEntry::make('roles.name')
                            ->label('Rôles attribués')
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
                            ->placeholder('Aucun rôle attribué'),
                    ]),

                Section::make('Informations étudiant')
                    ->schema([
                        TextEntry::make('matricule')
                            ->label('Numéro matricule')
                            ->placeholder('Non renseigné'),
                        TextEntry::make('classroom')
                            ->label('Classe')
                            ->placeholder('Non assignée'),
                        TextEntry::make('academic_year')
                            ->label('Année académique')
                            ->placeholder('Non définie'),
                    ])
                    ->columns(3)
                    ->visible(fn ($record) => $record->hasRole('parent_student')),

                Section::make('Informations parent 1')
                    ->schema([
                        TextEntry::make('parent1_name')
                            ->label('Prénom')
                            ->placeholder('Non renseigné'),
                        TextEntry::make('parent1_surname')
                            ->label('Nom')
                            ->placeholder('Non renseigné'),
                        TextEntry::make('parent1_phone')
                            ->label('Téléphone')
                            ->placeholder('Non renseigné'),
                    ])
                    ->columns(3)
                    ->visible(fn ($record) => $record->hasRole('parent_student')),

                Section::make('Informations parent 2')
                    ->schema([
                        TextEntry::make('parent2_name')
                            ->label('Prénom')
                            ->placeholder('Non renseigné'),
                        TextEntry::make('parent2_surname')
                            ->label('Nom')
                            ->placeholder('Non renseigné'),
                        TextEntry::make('parent2_phone')
                            ->label('Téléphone')
                            ->placeholder('Non renseigné'),
                    ])
                    ->columns(3)
                    ->visible(fn ($record) => $record->hasRole('parent_student')),
            ]);
    }
}