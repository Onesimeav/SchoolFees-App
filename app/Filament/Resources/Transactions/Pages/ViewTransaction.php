<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewTransaction extends ViewRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_receipt')
                ->label('Télécharger le reçu')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(fn () => route('transaction.receipt', $this->record))
                ->openUrlInNewTab()
                ->visible(fn () => $this->hasReceipt()),
        ];
    }

    private function hasReceipt(): bool
    {
        $tx = $this->record->loadMissing(['classRegistration', 'fee', 'installment']);

        return $tx->classRegistration !== null
            || ($tx->installment_id && $tx->kkiapay_reference)
            || ($tx->fee_id && $tx->kkiapay_reference && $tx->fee?->type === 'App\Models\GeneralFee');
    }
}