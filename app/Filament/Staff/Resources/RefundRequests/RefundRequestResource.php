<?php

namespace App\Filament\Staff\Resources\RefundRequests;

use App\Filament\Staff\Resources\RefundRequests\Pages\ListRefundRequests;
use App\Filament\Staff\Resources\RefundRequests\Pages\ViewRefundRequest;
use App\Filament\Staff\Resources\RefundRequests\Tables\RefundRequestsTable;
use App\Models\RefundRequest;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RefundRequestResource extends Resource
{
    protected static ?string $model = RefundRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUturnLeft;

    protected static ?string $navigationLabel = 'Remboursements';

    protected static ?string $modelLabel = 'Demande de remboursement';

    protected static ?string $pluralModelLabel = 'Demandes de remboursement';

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
        return parent::getEloquentQuery()->with(['user', 'transaction.fee']);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Étudiant')
                ->icon(Heroicon::OutlinedUser)
                ->columns(2)
                ->schema([
                    TextEntry::make('user.name')
                        ->label('Prénom')
                        ->placeholder('—'),
                    TextEntry::make('user.surname')
                        ->label('Nom')
                        ->placeholder('—'),
                    TextEntry::make('user.email')
                        ->label('Email')
                        ->copyable()
                        ->placeholder('—'),
                ]),

            Section::make('Transaction concernée')
                ->icon(Heroicon::OutlinedBanknotes)
                ->columns(2)
                ->schema([
                    TextEntry::make('transaction.fee.title')
                        ->label('Frais concerné')
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
                    TextEntry::make('transaction.kkiapay_reference')
                        ->label('Référence KKiaPay')
                        ->fontFamily('mono')
                        ->copyable()
                        ->placeholder('—'),
                    TextEntry::make('transaction.status')
                        ->label('Statut du paiement')
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
                ]),

            Section::make('Demande de remboursement')
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
                        ->label('Motif de la demande')
                        ->columnSpanFull()
                        ->placeholder('—'),
                    TextEntry::make('notes')
                        ->label('Notes de l\'administration')
                        ->columnSpanFull()
                        ->placeholder('—')
                        ->visible(fn ($record) => filled($record->notes)),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return RefundRequestsTable::configure($table);
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
            'index' => ListRefundRequests::route('/'),
            'view'  => ViewRefundRequest::route('/{record}'),
        ];
    }
}