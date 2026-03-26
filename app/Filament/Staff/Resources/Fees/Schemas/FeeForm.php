<?php

namespace App\Filament\Staff\Resources\Fees\Schemas;

use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
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
                        Select::make('grade_id')
                            ->label('Classe')
                            ->relationship('grade', 'name')
                            ->preload()
                            ->searchable()
                            ->required(fn ($get) => in_array($get('type'), [
                                'App\\Models\\RegistrationFee',
                                'App\\Models\\GeneralFee',
                            ]))
                            ->visible(fn ($get) => in_array($get('type'), [
                                'App\\Models\\RegistrationFee',
                                'App\\Models\\GeneralFee',
                            ])),
                    ])
                    ->columns(3),

                Section::make('Paramètres de versements')
                    ->schema([
                        Select::make('grade_id')
                            ->label('Classe')
                            ->relationship('grade', 'name')
                            ->preload()
                            ->searchable()
                            ->required()
                            ->helperText('Classe à laquelle ces frais de scolarité s\'appliquent'),
                        TextInput::make('late_fine_per_week')
                            ->label('Amende par semaine de retard')
                            ->numeric()
                            ->suffix('F CFA / semaine')
                            ->minValue(0)
                            ->step(1)
                            ->placeholder('0')
                            ->helperText('Montant de l\'amende appliquée par semaine de retard après la date d\'échéance du versement'),
                    ])
                    ->columns(2)
                    ->visible(fn ($get) => $get('type') === 'App\\Models\\TuitionFee'),

                Section::make('Versements')
                    ->description('Définissez les versements. Glissez-déposez pour réordonner après création.')
                    ->schema([
                        Repeater::make('installments')
                            ->relationship('installments')
                            ->schema([
                                TextInput::make('amount')
                                    ->label('Montant')
                                    ->required()
                                    ->numeric()
                                    ->suffix('F CFA')
                                    ->minValue(1)
                                    ->step(1)
                                    ->helperText(fn ($get) => filled($get('../../total_amount'))
                                        ? 'Total des frais : ' . number_format((float) $get('../../total_amount'), 0, ',', ' ') . ' F CFA'
                                        : null
                                    ),
                                DatePicker::make('due_date')
                                    ->label('Date d\'échéance')
                                    ->required()
                                    ->native(false),
                            ])
                            ->columns(2)
                            ->defaultItems(1)
                            ->minItems(1)
                            ->addActionLabel('Ajouter un versement')
                            ->columnSpanFull()
                            ->rules([
                                fn ($get): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get): void {
                                    $totalAmount = (float) ($get('total_amount') ?? 0);
                                    $items = is_array($value) ? array_values($value) : [];

                                    if ($totalAmount > 0) {
                                        $sum = (float) array_sum(array_column($items, 'amount'));
                                        if (abs($sum - $totalAmount) > 0.01) {
                                            $fail(
                                                'La somme des versements (' . number_format($sum, 0, ',', ' ') . ' F CFA) '
                                                . 'doit être égale au montant total des frais (' . number_format($totalAmount, 0, ',', ' ') . ' F CFA).'
                                            );
                                        }
                                    }

                                    $dates = array_filter(array_column($items, 'due_date'));
                                    if (count($dates) !== count(array_unique($dates))) {
                                        $fail('Deux versements ne peuvent pas avoir la même date d\'échéance.');
                                    }
                                },
                            ])
                            ->itemLabel(fn (array $state): ?string => filled($state['amount'])
                                ? number_format((float) $state['amount'], 0, ',', ' ') . ' F CFA'
                                    . (filled($state['due_date']) ? ' — échéance ' . \Carbon\Carbon::parse($state['due_date'])->format('d/m/Y') : '')
                                : null
                            ),
                    ])
                    ->visible(fn ($get, string $operation) =>
                        $get('type') === 'App\\Models\\TuitionFee' && $operation === 'create'
                    ),

                Section::make('Paramètres des frais généraux')
                    ->schema([
                        Toggle::make('required')
                            ->label('Frais obligatoire')
                            ->helperText('Ce frais est-il obligatoire pour tous les élèves ?')
                            ->default(false),
                        TextInput::make('late_fine_per_week')
                            ->label('Amende par semaine de retard')
                            ->numeric()
                            ->suffix('F CFA / semaine')
                            ->minValue(0)
                            ->step(1)
                            ->placeholder('0')
                            ->helperText('Montant de l\'amende appliquée par semaine de retard après la date d\'échéance'),
                    ])
                    ->columns(2)
                    ->visible(fn ($get) => $get('type') === 'App\\Models\\GeneralFee'),
            ]);
    }
}
