<?php

namespace App\Filament\Resources\Transactions;

use App\Filament\Resources\Fees\FeeResource;
use App\Filament\Resources\Transactions\Pages\ListTransactions;
use App\Filament\Resources\Transactions\Pages\ViewTransaction;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Staff\Resources\Transactions\Schemas\TransactionForm;
use App\Filament\Staff\Resources\Transactions\Tables\TransactionsTable;
use App\Models\Transaction;
use BackedEnum;
use Filament\Actions\Action as InfolistAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\IconPosition;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Transactions';

    protected static ?string $modelLabel = 'Transaction';

    protected static ?string $pluralModelLabel = 'Transactions';

    public static function form(Schema $schema): Schema
    {
        return TransactionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Étudiant')
                ->icon(Heroicon::OutlinedUser)
                ->columns(2)
                ->headerActions([
                    InfolistAction::make('view_user')
                        ->label('')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->url(fn ($record) => $record->user_id
                            ? UserResource::getUrl('view', ['record' => $record->user_id])
                            : null)
                        ->visible(fn ($record) => filled($record->user_id)),
                ])
                ->schema([
                    TextEntry::make('user.name')
                        ->label('Prénom')
                        ->placeholder('Non renseigné'),
                    TextEntry::make('user.surname')
                        ->label('Nom')
                        ->placeholder('Non renseigné'),
                    TextEntry::make('user.email')
                        ->label('Email')
                        ->icon('heroicon-s-clipboard-document')
                        ->iconPosition(IconPosition::After)
                        ->copyable()
                        ->placeholder('Non renseigné'),
                    TextEntry::make('user.phone_number')
                        ->label('Téléphone')
                        ->placeholder('Non renseigné'),
                ]),

            Section::make('Détails du paiement')
                ->icon(Heroicon::OutlinedBanknotes)
                ->columns(2)
                ->headerActions([
                    InfolistAction::make('view_fee')
                        ->label('')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->url(fn ($record) => $record->fee_id
                            ? FeeResource::getUrl('view', ['record' => $record->fee_id])
                            : null)
                        ->visible(fn ($record) => filled($record->fee_id)),
                ])
                ->schema([
                    TextEntry::make('fee.title')
                        ->label('Frais concerné')
                        ->placeholder('Non renseigné'),
                    TextEntry::make('fee.academic_year')
                        ->label('Année scolaire')
                        ->badge()
                        ->color('primary')
                        ->placeholder('Non renseigné'),
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
                        ->label('N° Mobile Money')
                        ->placeholder('Non renseigné'),
                ]),

            Section::make('Référence')
                ->icon(Heroicon::OutlinedIdentification)
                ->columns(2)
                ->schema([
                    TextEntry::make('kkiapay_reference')
                        ->label('Référence KKiaPay')
                        ->fontFamily('mono')
                        ->icon('heroicon-s-clipboard-document')
                        ->iconPosition(IconPosition::After)
                        ->copyable()
                        ->copyMessage('Référence copiée !')
                        ->placeholder('Non renseigné'),
                    TextEntry::make('created_at')
                        ->label('Créée le')
                        ->dateTime('d/m/Y à H:i'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return TransactionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransactions::route('/'),
            'view'  => ViewTransaction::route('/{record}'),
        ];
    }
}