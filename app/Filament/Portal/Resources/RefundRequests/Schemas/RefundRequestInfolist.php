<?php

namespace App\Filament\Portal\Resources\RefundRequests\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class RefundRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Frais concerné')
                ->icon(Heroicon::OutlinedBanknotes)
                ->columns(2)
                ->schema([
                    TextEntry::make('transaction.fee.title')
                        ->label('Intitulé')
                        ->placeholder('—'),
                    TextEntry::make('transaction.fee.academic_year')
                        ->label('Année scolaire')
                        ->badge()
                        ->color('primary')
                        ->placeholder('—'),
                    TextEntry::make('transaction.amount')
                        ->label('Montant payé')
                        ->formatStateUsing(fn ($state) => number_format($state, 0, ',', ' ') . ' F CFA')
                        ->weight('bold')
                        ->color('success'),
                    TextEntry::make('transaction.date')
                        ->label('Date du paiement')
                        ->date('d/m/Y'),
                ]),

            Section::make('Votre demande')
                ->icon(Heroicon::OutlinedArrowUturnLeft)
                ->columns(2)
                ->schema([
                    TextEntry::make('status')
                        ->label('Statut')
                        ->badge()
                        ->formatStateUsing(fn ($state) => match ($state) {
                            'pending'  => 'En attente',
                            'accepted' => 'Accepté',
                            'refused'  => 'Refusé',
                            default    => $state,
                        })
                        ->color(fn ($state) => match ($state) {
                            'pending'  => 'warning',
                            'accepted' => 'success',
                            'refused'  => 'danger',
                            default    => 'gray',
                        }),
                    TextEntry::make('created_at')
                        ->label('Soumis le')
                        ->dateTime('d/m/Y à H:i'),
                    TextEntry::make('reason')
                        ->label('Motif de votre demande')
                        ->columnSpanFull()
                        ->placeholder('—'),
                    TextEntry::make('notes')
                        ->label('Réponse de l\'administration')
                        ->columnSpanFull()
                        ->placeholder('—')
                        ->visible(fn ($record) => filled($record->notes)),
                ]),
        ]);
    }
}