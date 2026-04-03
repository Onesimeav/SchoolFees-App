<?php

namespace App\Filament\Resources\ClassRegistrations;

use App\Filament\Resources\ClassRegistrations\Pages\ListClassRegistrations;
use App\Filament\Resources\ClassRegistrations\Pages\ViewClassRegistration;
use App\Filament\Resources\ClassRegistrations\Tables\ClassRegistrationsTable;
use App\Filament\Resources\Fees\FeeResource;
use App\Filament\Resources\Transactions\TransactionResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\ClassRegistration;
use BackedEnum;
use Filament\Actions\Action as InfolistAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\IconPosition;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClassRegistrationResource extends Resource
{
    protected static ?string $model = ClassRegistration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Inscriptions';

    protected static ?string $modelLabel = 'Inscription';

    protected static ?string $pluralModelLabel = 'Inscriptions';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user', 'grade', 'transaction.fee']);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Informations de l\'élève')
                ->icon(Heroicon::OutlinedUser)
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
                ])
                ->columns(2),

            Section::make('Classe demandée')
                ->icon(Heroicon::OutlinedAcademicCap)
                ->schema([
                    TextEntry::make('grade.name')
                        ->label('Classe')
                        ->badge()
                        ->color('info'),
                    TextEntry::make('grade.description')
                        ->label('Description')
                        ->placeholder('Non renseigné')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Statut de l\'inscription')
                ->icon(Heroicon::OutlinedClipboardDocumentCheck)
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
                    TextEntry::make('notes')
                        ->label('Motif du refus')
                        ->placeholder('Non renseigné')
                        ->columnSpanFull()
                        ->visible(fn ($record) => $record->status === 'refused'),
                ])
                ->columns(2),

            Section::make('Paiement associé')
                ->icon(Heroicon::OutlinedCreditCard)
                ->headerActions([
                    InfolistAction::make('view_transaction')
                        ->label('')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->url(fn ($record) => $record->transaction_id
                            ? TransactionResource::getUrl('view', ['record' => $record->transaction_id])
                            : null)
                        ->visible(fn ($record) => filled($record->transaction_id)),
                    InfolistAction::make('view_fee')
                        ->label('')
                        ->icon('heroicon-o-rectangle-stack')
                        ->url(fn ($record) => $record->transaction?->fee_id
                            ? FeeResource::getUrl('view', ['record' => $record->transaction->fee_id])
                            : null)
                        ->visible(fn ($record) => filled($record->transaction?->fee_id)),
                ])
                ->schema([
                    TextEntry::make('transaction.amount')
                        ->label('Montant payé')
                        ->money('XOF')
                        ->placeholder('Non renseigné'),
                    TextEntry::make('transaction.date')
                        ->label('Date de paiement')
                        ->date('d/m/Y')
                        ->placeholder('Non renseigné'),
                    TextEntry::make('transaction.status')
                        ->label('Statut du paiement')
                        ->badge()
                        ->formatStateUsing(fn ($state) => match ($state) {
                            'pending'   => 'En attente',
                            'completed' => 'Complété',
                            'failed'    => 'Échoué',
                            'refunded'  => 'Remboursé',
                            default     => $state ?? 'Non renseigné',
                        })
                        ->color(fn ($state) => match ($state) {
                            'pending'   => 'warning',
                            'completed' => 'success',
                            'failed'    => 'danger',
                            'refunded'  => 'info',
                            default     => 'gray',
                        }),
                    TextEntry::make('transaction.phone_number')
                        ->label('N° Mobile Money')
                        ->placeholder('Non renseigné'),
                    TextEntry::make('transaction.fee.title')
                        ->label('Frais concerné')
                        ->placeholder('Non renseigné')
                        ->columnSpanFull(),
                ])
                ->columns(2),

        ]);
    }

    public static function table(Table $table): Table
    {
        return ClassRegistrationsTable::configure($table);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClassRegistrations::route('/'),
            'view'  => ViewClassRegistration::route('/{record}'),
        ];
    }
}