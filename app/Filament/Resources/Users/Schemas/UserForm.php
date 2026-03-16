<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('surname')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('phone_number')
                            ->tel()
                            ->maxLength(255),
                        TextInput::make('password')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->maxLength(255)
                            ->helperText(fn (string $context): string =>
                                $context === 'edit' ? 'Leave blank to keep current password.' : ''
                            ),
                        Toggle::make('verified')
                            ->label('Email Verified')
                            ->default(false),
                    ])
                    ->columns(2),

                Section::make('Role Assignment')
                    ->schema([
                        Select::make('roles')
                            ->label('Roles')
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->preload()
                            ->searchable()
                            ->live()
                            ->helperText('Select one or more roles for this user'),
                    ]),

                Section::make('Student Information')
                    ->schema([
                        TextInput::make('matricule')
                            ->label('Matricule Number')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('classroom')
                            ->label('Classroom')
                            ->maxLength(255),
                        TextInput::make('academic_year')
                            ->label('Academic Year')
                            ->maxLength(255)
                            ->placeholder('e.g., 2025-2026'),
                    ])
                    ->columns(3)
                    ->visible(fn ($get) =>
                        is_array($get('roles')) && in_array('parent_student',
                            Role::whereIn('id', $get('roles'))->pluck('name')->toArray()
                        )
                    ),

                Section::make('Parent 1 Information')
                    ->schema([
                        TextInput::make('parent1_name')
                            ->label('Parent 1 Name')
                            ->maxLength(255),
                        TextInput::make('parent1_surname')
                            ->label('Parent 1 Surname')
                            ->maxLength(255),
                        TextInput::make('parent1_phone')
                            ->label('Parent 1 Phone')
                            ->tel()
                            ->maxLength(255),
                    ])
                    ->columns(3)
                    ->visible(fn ($get) =>
                        is_array($get('roles')) && in_array('parent_student',
                            Role::whereIn('id', $get('roles'))->pluck('name')->toArray()
                        )
                    ),

                Section::make('Parent 2 Information')
                    ->schema([
                        TextInput::make('parent2_name')
                            ->label('Parent 2 Name')
                            ->maxLength(255),
                        TextInput::make('parent2_surname')
                            ->label('Parent 2 Surname')
                            ->maxLength(255),
                        TextInput::make('parent2_phone')
                            ->label('Parent 2 Phone')
                            ->tel()
                            ->maxLength(255),
                    ])
                    ->columns(3)
                    ->visible(fn ($get) =>
                        is_array($get('roles')) && in_array('parent_student',
                            Role::whereIn('id', $get('roles'))->pluck('name')->toArray()
                        )
                    ),
            ]);
    }
}
