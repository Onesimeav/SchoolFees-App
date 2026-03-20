<?php

namespace App\Filament\Resources\ClassRegistrations;

use App\Filament\Resources\ClassRegistrations\Pages\ListClassRegistrations;
use App\Filament\Resources\ClassRegistrations\Pages\ViewClassRegistration;
use App\Filament\Resources\ClassRegistrations\Tables\ClassRegistrationsTable;
use App\Models\ClassRegistration;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
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
                ->schema([
                    TextEntry::make('user.name')
                        ->label('Prénom')
                        ->default('—'),
                    TextEntry::make('user.surname')
                        ->label('Nom')
                        ->default('—'),
                    TextEntry::make('user.email')
                        ->label('Email')
                        ->copyable()
                        ->default('—'),
                    TextEntry::make('user.phone_number')
                        ->label('Téléphone')
                        ->default('—'),
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
                        ->default('—')
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
                        ->default('—')
                        ->columnSpanFull()
                        ->visible(fn ($record) => $record->status === 'refused'),
                ])
                ->columns(2),

            Section::make('Paiement associé')
                ->icon(Heroicon::OutlinedCreditCard)
                ->schema([
                    TextEntry::make('transaction.amount')
                        ->label('Montant payé')
                        ->money('XOF')
                        ->default('—'),
                    TextEntry::make('transaction.date')
                        ->label('Date de paiement')
                        ->date('d/m/Y')
                        ->default('—'),
                    TextEntry::make('transaction.status')
                        ->label('Statut du paiement')
                        ->badge()
                        ->formatStateUsing(fn ($state) => match ($state) {
                            'pending'   => 'En attente',
                            'completed' => 'Complété',
                            'failed'    => 'Échoué',
                            'refunded'  => 'Remboursé',
                            default     => $state ?? '—',
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
                        ->default('—'),
                    TextEntry::make('transaction.fee.title')
                        ->label('Frais concerné')
                        ->default('—')
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