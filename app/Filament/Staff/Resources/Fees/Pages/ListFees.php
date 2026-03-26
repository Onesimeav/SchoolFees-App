<?php

namespace App\Filament\Staff\Resources\Fees\Pages;

use App\Filament\Staff\Resources\Fees\FeeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFees extends ListRecords
{
    protected static string $resource = FeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nouveau')
                ->icon('heroicon-o-plus')
                ->visible(fn () => auth()->user()?->hasAnyRole(['admin', 'secretary'])),
        ];
    }
}
