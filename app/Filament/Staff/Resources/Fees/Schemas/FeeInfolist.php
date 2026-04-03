<?php

namespace App\Filament\Staff\Resources\Fees\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class FeeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Informations générales')
                ->icon(Heroicon::OutlinedRectangleStack)
                ->schema([
                    TextEntry::make('type')
                        ->label('Type de frais')
                        ->badge()
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'App\\Models\\RegistrationFee' => 'Frais d\'inscription',
                            'App\\Models\\TuitionFee'      => 'Frais de scolarité',
                            'App\\Models\\GeneralFee'      => 'Frais généraux',
                            default                        => $state,
                        })
                        ->color(fn (string $state): string => match ($state) {
                            'App\\Models\\RegistrationFee' => 'info',
                            'App\\Models\\TuitionFee'      => 'success',
                            'App\\Models\\GeneralFee'      => 'warning',
                            default                        => 'gray',
                        }),

                    TextEntry::make('title')
                        ->label('Intitulé'),

                    TextEntry::make('academic_year')
                        ->label('Année scolaire')
                        ->badge()
                        ->color('gray'),

                    TextEntry::make('description')
                        ->label('Description')
                        ->placeholder('—')
                        ->columnSpanFull(),
                ])
                ->columns(3),

            Section::make('Classe')
                ->icon(Heroicon::OutlinedAcademicCap)
                ->schema([
                    TextEntry::make('grade.name')
                        ->label('Classe')
                        ->badge()
                        ->color('info')
                        ->placeholder('—'),
                ])
                ->visible(fn ($record) => in_array($record->type, [
                    'App\\Models\\RegistrationFee',
                    'App\\Models\\GeneralFee',
                ])),

            Section::make('Montant et échéance')
                ->icon(Heroicon::OutlinedCreditCard)
                ->schema([
                    TextEntry::make('total_amount')
                        ->label('Montant total')
                        ->money('XOF'),

                    TextEntry::make('due_before')
                        ->label('Date limite')
                        ->date('d/m/Y')
                        ->placeholder('—')
                        ->visible(fn ($record) => $record->type !== 'App\\Models\\TuitionFee'),
                ])
                ->columns(2),

            Section::make('Paramètres de scolarité')
                ->icon(Heroicon::OutlinedBanknotes)
                ->schema([
                    TextEntry::make('grade.name')
                        ->label('Classe')
                        ->badge()
                        ->color('info')
                        ->placeholder('—'),

                    TextEntry::make('number_of_installments')
                        ->label('Nombre de versements')
                        ->badge()
                        ->color('primary')
                        ->placeholder('—'),

                    TextEntry::make('late_fine_per_week')
                        ->label('Amende par semaine de retard')
                        ->money('XOF')
                        ->placeholder('Aucune'),
                ])
                ->columns(3)
                ->visible(fn ($record) => $record->type === 'App\\Models\\TuitionFee'),

            Section::make('Paramètres des frais généraux')
                ->icon(Heroicon::OutlinedCog6Tooth)
                ->schema([
                    IconEntry::make('required')
                        ->label('Obligatoire')
                        ->boolean(),

                    TextEntry::make('late_fine_per_week')
                        ->label('Amende par semaine de retard')
                        ->money('XOF')
                        ->placeholder('Aucune'),
                ])
                ->columns(2)
                ->visible(fn ($record) => $record->type === 'App\\Models\\GeneralFee'),

        ]);
    }
}
