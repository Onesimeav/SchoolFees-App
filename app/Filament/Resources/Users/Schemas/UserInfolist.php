<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Name'),
                        TextEntry::make('surname')
                            ->label('Surname'),
                        TextEntry::make('email')
                            ->label('Email Address')
                            ->copyable(),
                        TextEntry::make('phone_number')
                            ->label('Phone Number')
                            ->placeholder('Not provided'),
                        IconEntry::make('verified')
                            ->label('Email Verified')
                            ->boolean(),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                    ])
                    ->columns(2),

                Section::make('Roles')
                    ->schema([
                        TextEntry::make('roles.name')
                            ->label('Assigned Roles')
                            ->badge()
                            ->colors([
                                'danger' => 'admin',
                                'warning' => 'accountant',
                                'info' => 'secretary',
                                'success' => 'parent_student',
                                'gray' => 'employee',
                            ])
                            ->placeholder('No roles assigned'),
                    ]),

                Section::make('Student Information')
                    ->schema([
                        TextEntry::make('matricule')
                            ->label('Matricule Number')
                            ->placeholder('Not provided'),
                        TextEntry::make('classroom')
                            ->label('Classroom')
                            ->placeholder('Not assigned'),
                        TextEntry::make('academic_year')
                            ->label('Academic Year')
                            ->placeholder('Not set'),
                    ])
                    ->columns(3)
                    ->visible(fn ($record) => $record->hasRole('parent_student')),

                Section::make('Parent 1 Information')
                    ->schema([
                        TextEntry::make('parent1_name')
                            ->label('Name')
                            ->placeholder('Not provided'),
                        TextEntry::make('parent1_surname')
                            ->label('Surname')
                            ->placeholder('Not provided'),
                        TextEntry::make('parent1_phone')
                            ->label('Phone')
                            ->placeholder('Not provided'),
                    ])
                    ->columns(3)
                    ->visible(fn ($record) => $record->hasRole('parent_student')),

                Section::make('Parent 2 Information')
                    ->schema([
                        TextEntry::make('parent2_name')
                            ->label('Name')
                            ->placeholder('Not provided'),
                        TextEntry::make('parent2_surname')
                            ->label('Surname')
                            ->placeholder('Not provided'),
                        TextEntry::make('parent2_phone')
                            ->label('Phone')
                            ->placeholder('Not provided'),
                    ])
                    ->columns(3)
                    ->visible(fn ($record) => $record->hasRole('parent_student')),
            ]);
    }
}
