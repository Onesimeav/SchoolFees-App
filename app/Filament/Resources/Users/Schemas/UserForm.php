<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations générales')
                    ->schema([
                        TextInput::make('name')
                            ->label('Prénom')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('surname')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Adresse email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('phone_number')
                            ->label('Téléphone')
                            ->tel()
                            ->maxLength(255),
                        TextInput::make('password')
                            ->label('Nouveau mot de passe')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->maxLength(255)
                            ->helperText('Laisser vide pour conserver le mot de passe actuel.')
                            ->visibleOn('edit'),
                    ])
                    ->columns(2),

                Section::make('Attribution des rôles')
                    ->schema([
                        Select::make('roles')
                            ->label('Rôle')
                            ->relationship('roles', 'name')
                            ->preload()
                            ->searchable()
                            ->live()
                            ->getOptionLabelFromRecordUsing(fn ($record) => match($record->name) {
                                'admin' => 'Administrateur',
                                'accountant' => 'Comptable',
                                'secretary' => 'Secrétaire',
                                'employee' => 'Employé',
                                'parent_student' => 'Parent / Élève',
                                default => $record->name,
                            })
                            ->helperText('Sélectionner un rôle pour cet utilisateur'),
                    ]),

                Section::make('Informations étudiant')
                    ->schema([
                        TextInput::make('matricule')
                            ->label('Numéro matricule')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('classroom')
                            ->label('Classe')
                            ->maxLength(255),
                        TextInput::make('academic_year')
                            ->label('Année académique')
                            ->maxLength(255)
                            ->placeholder('ex : 2025-2026'),
                    ])
                    ->columns(3)
                    ->visible(fn ($get) => Role::find($get('roles'))?->name === 'parent_student'),

                Section::make('Informations parent 1')
                    ->schema([
                        TextInput::make('parent1_name')
                            ->label('Prénom du parent 1')
                            ->maxLength(255),
                        TextInput::make('parent1_surname')
                            ->label('Nom du parent 1')
                            ->maxLength(255),
                        TextInput::make('parent1_phone')
                            ->label('Téléphone du parent 1')
                            ->tel()
                            ->maxLength(255),
                    ])
                    ->columns(3)
                    ->visible(fn ($get) => Role::find($get('roles'))?->name === 'parent_student'),

                Section::make('Informations parent 2')
                    ->schema([
                        TextInput::make('parent2_name')
                            ->label('Prénom du parent 2')
                            ->maxLength(255),
                        TextInput::make('parent2_surname')
                            ->label('Nom du parent 2')
                            ->maxLength(255),
                        TextInput::make('parent2_phone')
                            ->label('Téléphone du parent 2')
                            ->tel()
                            ->maxLength(255),
                    ])
                    ->columns(3)
                    ->visible(fn ($get) => Role::find($get('roles'))?->name === 'parent_student'),
            ]);
    }
}