<?php

namespace App\Filament\Resources\ClassRegistrations\Pages;

use App\Filament\Resources\ClassRegistrations\ClassRegistrationResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewClassRegistration extends ViewRecord
{
    protected static string $resource = ClassRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('accept')
                ->label('Accepter')
                ->color('success')
                ->icon(Heroicon::OutlinedCheck)
                ->visible(fn () => $this->record->status === 'pending')
                ->requiresConfirmation()
                ->modalHeading('Accepter l\'inscription')
                ->modalDescription('Confirmer l\'acceptation de cette inscription ?')
                ->action(function () {
                    $this->record->update(['status' => 'accepted']);
                    Notification::make()->title('Inscription acceptée')->success()->send();
                    $this->refreshFormData(['status']);
                }),

            Action::make('refuse')
                ->label('Refuser')
                ->color('danger')
                ->icon(Heroicon::OutlinedXMark)
                ->visible(fn () => $this->record->status === 'pending')
                ->form([
                    Textarea::make('notes')
                        ->label('Motif du refus')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    $this->record->update(['status' => 'refused', 'notes' => $data['notes']]);
                    Notification::make()->title('Inscription refusée')->success()->send();
                    $this->refreshFormData(['status', 'notes']);
                }),

            DeleteAction::make(),
        ];
    }
}