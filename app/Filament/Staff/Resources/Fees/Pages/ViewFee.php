<?php

namespace App\Filament\Staff\Resources\Fees\Pages;

use App\Filament\Staff\Resources\Fees\FeeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewFee extends ViewRecord
{
    protected static string $resource = FeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->icon('heroicon-o-pencil')
                ->visible(fn () => auth()->user()?->hasAnyRole(['admin', 'secretary'])),
        ];
    }
}