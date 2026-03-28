<?php

namespace App\Filament\Staff\Resources\Transactions\Tables;

use App\Mail\RefundConfirmationMail;
use App\Models\RefundRequest;
use App\Models\User;
use App\Services\KkiapayService;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.full_name')
                    ->label('Étudiant')
                    ->searchable(['name', 'surname'])
                    ->sortable(['name', 'surname'])
                    ->description(fn ($record) => $record->user?->email),
                TextColumn::make('fee.title')
                    ->label('Frais')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->fee?->title),
                TextColumn::make('installment.number')
                    ->label('Versement')
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn ($state) => $state ? 'N°' . $state : '—')
                    ->toggleable()
                    ->placeholder('N/A'),
                TextColumn::make('amount')
                    ->label('Montant')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', ' ') . ' F CFA')
                    ->sortable(),
                TextColumn::make('date')
                    ->label('Date de paiement')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('status')
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
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone_number')
                    ->label('Téléphone')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('kkiapay_reference')
                    ->label('Référence')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('—')
                    ->copyable()
                    ->fontFamily('mono'),
                TextColumn::make('created_at')
                    ->label('Créée le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending'   => 'En attente',
                        'completed' => 'Complété',
                        'failed'    => 'Échoué',
                        'refunded'  => 'Remboursé',
                    ])
                    ->multiple(),
                SelectFilter::make('user')
                    ->label('Étudiant')
                    ->relationship('user', 'name')
                    ->getOptionLabelFromRecordUsing(fn (User $record) => $record->name . ' ' . $record->surname)
                    ->searchable()
                    ->preload(),
                SelectFilter::make('fee')
                    ->label('Frais')
                    ->relationship('fee', 'title')
                    ->searchable()
                    ->preload(),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->deferFilters(false)
            ->recordActions([
                ViewAction::make()
                    ->label('Détails'),
                Action::make('process')
                    ->label('Valider')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Valider la transaction')
                    ->modalDescription('Êtes-vous sûr de vouloir marquer cette transaction comme complétée ?')
                    ->modalSubmitActionLabel('Valider')
                    ->visible(fn ($record) => $record->status === 'pending'
                        && auth()->user()?->hasAnyRole(['admin', 'secretary']))
                    ->action(function ($record) {
                        $record->update(['status' => 'completed']);
                        Notification::make()
                            ->title('Transaction validée')
                            ->success()
                            ->send();
                    }),
                Action::make('refund')
                    ->label('Rembourser')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Rembourser la transaction')
                    ->modalDescription('Le remboursement sera traité via KKiaPay, le frais sera marqué non payé et un email de confirmation sera envoyé à l\'étudiant.')
                    ->modalSubmitActionLabel('Rembourser')
                    ->visible(fn ($record) => $record->status === 'completed'
                        && auth()->user()?->hasAnyRole(['admin', 'accountant']))
                    ->action(function ($record) {
                        if (! app(KkiapayService::class)->refund($record->kkiapay_reference)) {
                            Notification::make()
                                ->title('Échec du remboursement')
                                ->body('KKiaPay n\'a pas pu traiter le remboursement. Veuillez réessayer.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $record->update(['status' => 'refunded']);

                        $refundRequest = RefundRequest::create([
                            'transaction_id' => $record->id,
                            'user_id'        => $record->user_id,
                            'reason'         => 'Remboursement initié par l\'administration.',
                            'status'         => 'accepted',
                        ]);

                        $refundRequest->load(['transaction.fee.grade', 'user']);

                        $pdf     = Pdf::loadView('pdf.refund-receipt', ['refundRequest' => $refundRequest]);
                        $pdfPath = 'receipts/' . $record->user_id . '/refund-' . $record->kkiapay_reference . '.pdf';
                        Storage::disk('supabase')->put($pdfPath, $pdf->output());

                        Mail::to($record->user->email)
                            ->queue(new RefundConfirmationMail($refundRequest, $pdfPath));

                        Notification::make()
                            ->title('Transaction remboursée')
                            ->body('Le remboursement a été traité et un email a été envoyé à l\'étudiant.')
                            ->success()
                            ->send();
                    }),
                EditAction::make()
                    ->label('Modifier')
                    ->visible(fn ($record) => $record->status === 'pending'
                        && auth()->user()?->hasAnyRole(['admin', 'secretary'])),
                DeleteAction::make()
                    ->label('Supprimer')
                    ->visible(fn ($record) => auth()->user()?->hasRole('admin')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Supprimer la sélection')
                        ->visible(fn () => auth()->user()?->hasRole('admin')),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
