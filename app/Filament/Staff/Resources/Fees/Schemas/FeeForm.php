<?php

namespace App\Filament\Staff\Resources\Fees\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Fee Details')
                    ->schema([
                        Select::make('type')
                            ->label('Fee Type')
                            ->options([
                                'App\\Models\\RegistrationFee' => 'Registration Fee',
                                'App\\Models\\TuitionFee' => 'Tuition Fee',
                                'App\\Models\\GeneralFee' => 'General Fee',
                            ])
                            ->required()
                            ->live()
                            ->helperText('Select the type of fee you want to create'),
                        TextInput::make('title')
                            ->label('Fee Title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., School Registration 2025-2026'),
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Amount & Period')
                    ->schema([
                        TextInput::make('total_amount')
                            ->label('Total Amount')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0)
                            ->step(0.01),
                        TextInput::make('academic_year')
                            ->label('Academic Year')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., 2025-2026'),
                        TextInput::make('classroom')
                            ->label('Classroom/Grade')
                            ->maxLength(255)
                            ->placeholder('e.g., Grade 10, Form 3'),
                    ])
                    ->columns(3),

                Section::make('Installment Settings')
                    ->schema([
                        TextInput::make('number_of_installments')
                            ->label('Number of Installments')
                            ->required(fn ($get) => $get('type') === 'App\\Models\\TuitionFee')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(12)
                            ->default(1)
                            ->helperText('How many installments should this tuition fee be divided into?'),
                    ])
                    ->visible(fn ($get) => $get('type') === 'App\\Models\\TuitionFee'),

                Section::make('General Fee Settings')
                    ->schema([
                        Toggle::make('required')
                            ->label('Required Fee')
                            ->helperText('Is this a required fee for all students?')
                            ->default(false),
                    ])
                    ->visible(fn ($get) => $get('type') === 'App\\Models\\GeneralFee'),
            ]);
    }
}
