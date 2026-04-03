<?php

namespace App\Filament\Staff\Resources\Fees\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations générales')
                    ->schema([
                        Select::make('type')
                            ->label('Type de frais')
                            ->options([
                                'App\\Models\\RegistrationFee' => 'Frais d\'inscription',
                                'App\\Models\\TuitionFee' => 'Frais de scolarité',
                                'App\\Models\\GeneralFee' => 'Frais généraux',
                            ])
                            ->required()
                            ->live()
                            ->helperText('Sélectionnez le type de frais à créer'),
                        TextInput::make('title')
                            ->label('Intitulé')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('ex : Inscription 2025-2026'),
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Montant et période')
                    ->schema([
                        TextInput::make('total_amount')
                            ->label('Montant total')
                            ->required()
                            ->numeric()
                            ->suffix('F CFA')
                            ->minValue(0)
                            ->step(1),
                        TextInput::make('academic_year')
                            ->label('Année scolaire')
                            ->required()
                            ->maxLength(9)
                            ->placeholder('ex : 2025-2026')
                            ->helperText('Format : AAAA-AAAA+1 (ex : 2025-2026)')
                            ->rules([
                                fn () => function (string $attribute, mixed $value, \Closure $fail) {
                                    if (! preg_match('/^\d{4}-\d{4}$/', $value)) {
                                        $fail('Le format doit être AAAA-AAAA (ex : 2025-2026).');
                                        return;
                                    }
                                    [$start, $end] = explode('-', $value);
                                    if ((int) $end !== (int) $start + 1) {
                                        $fail('L\'écart entre les deux années doit être exactement de 1 an.');
                                    }
                                },
                            ]),
                        DatePicker::make('due_before')
                            ->label('Date limite')
                            ->native(false)
                            ->required(fn ($get) => in_array($get('type'), [
                                'App\\Models\\RegistrationFee',
                                'App\\Models\\GeneralFee',
                            ]))
                            ->visible(fn ($get) => $get('type') !== 'App\\Models\\TuitionFee')
                            ->helperText('Date limite de paiement pour ce frais'),
                        TextInput::make('classroom')
                            ->label('Classe')
                            ->maxLength(255)
                            ->placeholder('ex : Terminale A, CE2')
                            ->visible(fn ($get) => $get('type') !== 'App\\Models\\RegistrationFee'),

                        Select::make('grade_id')
                            ->label('Classe')
                            ->relationship('grade', 'name')
                            ->preload()
                            ->searchable()
                            ->required(fn ($get) => $get('type') === 'App\\Models\\RegistrationFee')
                            ->visible(fn ($get) => $get('type') === 'App\\Models\\RegistrationFee'),
                    ])
                    ->columns(3),

                Section::make('Paramètres de versements')
                    ->schema([
                        TextInput::make('number_of_installments')
                            ->label('Nombre de versements')
                            ->required(fn ($get) => $get('type') === 'App\\Models\\TuitionFee')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(12)
                            ->default(1)
                            ->helperText('En combien de versements ces frais de scolarité doivent-ils être répartis ?'),
                    ])
                    ->visible(fn ($get) => $get('type') === 'App\\Models\\TuitionFee'),

                Section::make('Paramètres des frais généraux')
                    ->schema([
                        Toggle::make('required')
                            ->label('Frais obligatoire')
                            ->helperText('Ce frais est-il obligatoire pour tous les élèves ?')
                            ->default(false),
                    ])
                    ->visible(fn ($get) => $get('type') === 'App\\Models\\GeneralFee'),
            ]);
    }
}
