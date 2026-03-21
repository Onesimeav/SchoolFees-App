<?php

namespace App\Filament\Portal\Resources\Transactions\Pages;

use App\Filament\Portal\Resources\Transactions\TransactionResource;
use App\Models\RefundRequest;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

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
                ->url(fn () => $this->receiptUrl())
                ->openUrlInNewTab()
                ->visible(fn () => $this->hasReceipt()),

            Action::make('request_refund')
                ->label('Demander un remboursement')
                ->icon(Heroicon::OutlinedArrowUturnLeft)
                ->color('warning')
                ->visible(fn () =>
                    $this->record->status === 'completed'
                    && $this->record->classRegistration === null
                    && ! $this->record->refundRequests()
                        ->whereIn('status', ['pending', 'accepted'])->exists()
                )
                ->form([
                    Textarea::make('reason')
                        ->label('Motif de la demande')
                        ->required()
                        ->rows(4),
                ])
                ->action(function (array $data) {
                    RefundRequest::create([
                        'transaction_id' => $this->record->id,
                        'user_id'        => auth()->id(),
                        'reason'         => $data['reason'],
                        'status'         => 'pending',
                    ]);

                    Notification::make()
                        ->title('Demande envoyée')
                        ->body('Votre demande de remboursement a été transmise à l\'administration.')
                        ->success()
                        ->send();
                }),
        ];
    }

    private function hasReceipt(): bool
    {
        $tx = $this->record->loadMissing(['classRegistration', 'fee', 'installment']);

        return $tx->classRegistration !== null
            || ($tx->installment_id && $tx->kkiapay_reference)
            || ($tx->fee_id && $tx->kkiapay_reference && $tx->fee?->type === 'App\Models\GeneralFee');
    }

    private function receiptUrl(): ?string
    {
        $tx = $this->record->loadMissing(['classRegistration', 'fee', 'installment']);

        if ($tx->classRegistration !== null) {
            return route('portal.transaction.receipt', $tx);
        }

        if ($tx->installment_id && $tx->kkiapay_reference) {
            return route('portal.transaction.tuition-receipt', $tx);
        }

        if ($tx->fee_id && $tx->kkiapay_reference && $tx->fee?->type === 'App\Models\GeneralFee') {
            return route('portal.transaction.general-fee-receipt', $tx);
        }

        return null;
    }
}