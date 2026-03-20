<?php

namespace App\Filament\Portal\Resources\Transactions\Pages;

use App\Filament\Portal\Resources\Transactions\TransactionResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewTransaction extends ViewRecord
{
    protected static string $resource = TransactionResource::class;

    public function getTitle(): string
    {
        return 'Détails de la transaction';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_receipt')
                ->label('Télécharger le reçu')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->url(fn () => route('portal.transaction.receipt', $this->record))
                ->openUrlInNewTab()
                ->visible(fn () => $this->record->classRegistration !== null),
        ];
    }
}