<?php

namespace App\Filament\Resources\RefundRequests\Pages;

use App\Filament\Resources\RefundRequests\RefundRequestResource;
use App\Mail\RefundConfirmationMail;
use App\Services\KkiapayService;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ViewRefundRequest extends ViewRecord
{
    protected static string $resource = RefundRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('accept')
                ->label('Accepter le remboursement')
                ->color('success')
                ->icon(Heroicon::OutlinedCheck)
                ->visible(fn () => auth()->user()?->hasAnyRole(['admin', 'accountant'])
                    && $this->record->status === 'pending')
                ->requiresConfirmation()
                ->modalHeading('Accepter la demande de remboursement')
                ->modalDescription('Le remboursement sera traité via KKiaPay et le paiement sera annulé.')
                ->action(function () {
                    $tx = $this->record->transaction;

                    if (! app(KkiapayService::class)->refund($tx->kkiapay_reference)) {
                        Notification::make()
                            ->title('Échec du remboursement')
                            ->body('KKiaPay n\'a pas pu traiter le remboursement. Veuillez réessayer.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $tx->update(['status' => 'refunded']);
                    $this->record->update(['status' => 'accepted']);

                    $pdf     = Pdf::loadView('pdf.refund-receipt', ['refundRequest' => $this->record->load(['transaction.fee.grade', 'user'])]);
                    $pdfPath = 'receipts/' . $this->record->user_id . '/refund-' . $tx->kkiapay_reference . '.pdf';
                    Storage::disk('supabase')->put($pdfPath, $pdf->output());

                    Mail::to($this->record->user->email)
                        ->queue(new RefundConfirmationMail($this->record, $pdfPath));

                    Notification::make()
                        ->title('Remboursement accepté')
                        ->body('Le remboursement a été traité et un email de confirmation a été envoyé.')
                        ->success()
                        ->send();

                    $this->refreshFormData(['status']);
                }),

            Action::make('refuse')
                ->label('Refuser')
                ->color('danger')
                ->icon(Heroicon::OutlinedXMark)
                ->visible(fn () => auth()->user()?->hasAnyRole(['admin', 'accountant'])
                    && $this->record->status === 'pending')
                ->form([
                    Textarea::make('notes')
                        ->label('Motif du refus')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'status' => 'refused',
                        'notes'  => $data['notes'],
                    ]);

                    Notification::make()
                        ->title('Demande refusée')
                        ->success()
                        ->send();

                    $this->refreshFormData(['status', 'notes']);
                }),

            Action::make('download_receipt')
                ->label('Télécharger le reçu')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(fn () => route('refund.receipt', $this->record))
                ->openUrlInNewTab()
                ->visible(fn () => $this->record->status === 'accepted'
                    && filled($this->record->transaction?->kkiapay_reference)),
        ];
    }
}