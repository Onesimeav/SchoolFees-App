<?php

namespace App\Filament\Portal\Resources\Transactions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class TransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Détails du paiement')
                    ->description('Informations sur le paiement effectué.')
                    ->icon('heroicon-o-banknotes')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('fee.title')
                            ->label('Frais concerné')
                            ->weight('semibold')
                            ->placeholder('—'),
                        TextEntry::make('fee.academic_year')
                            ->label('Année scolaire')
                            ->badge()
                            ->color('primary')
                            ->placeholder('—'),
                        TextEntry::make('amount')
                            ->label('Montant payé')
                            ->formatStateUsing(fn ($state) => number_format($state, 0, ',', ' ') . ' F CFA')
                            ->weight('bold')
                            ->color('success'),
                        TextEntry::make('status')
                            ->label('Statut')
                            ->badge()
                            ->formatStateUsing(fn ($state) => match ($state) {
                                'pending'   => 'En attente',
                                'completed' => 'Complété',
                                'failed'    => 'Échoué',
                                'refunded'  => 'Remboursé',
                                default     => $state,
                            })
                            ->color(fn ($state) => match ($state) {
                                'pending'   => 'warning',
                                'completed' => 'success',
                                'failed'    => 'danger',
                                'refunded'  => 'info',
                                default     => 'gray',
                            }),
                        TextEntry::make('date')
                            ->label('Date du paiement')
                            ->date('d/m/Y'),
                        TextEntry::make('phone_number')
                            ->label('Numéro de téléphone')
                            ->icon('heroicon-m-phone')
                            ->placeholder('—'),
                    ]),

                Section::make('Référence & traçabilité')
                    ->description('Identifiants pour le suivi de votre transaction.')
                    ->icon('heroicon-o-identification')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('kkiapay_reference')
                            ->label('Référence KKiaPay')
                            ->fontFamily('mono')
                            ->copyable()
                            ->copyMessage('Référence copiée !')
                            ->copyMessageDuration(1500)
                            ->placeholder('—'),
                        TextEntry::make('grade_name')
                            ->label('Classe concernée')
                            ->getStateUsing(fn (Model $record) =>
                                $record->classRegistration?->grade?->name
                                ?? $record->fee?->grade?->name
                                ?? null
                            )
                            ->badge()
                            ->color('primary')
                            ->placeholder('—'),
                        TextEntry::make('created_at')
                            ->label('Créée le')
                            ->dateTime('d/m/Y à H:i')
                            ->placeholder('—'),
                        TextEntry::make('updated_at')
                            ->label('Mise à jour le')
                            ->dateTime('d/m/Y à H:i')
                            ->placeholder('—'),
                    ]),
            ]);
    }
}