<?php

namespace App\Filament\Portal\Resources\RefundRequests\Pages;

use App\Filament\Portal\Resources\RefundRequests\RefundRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListRefundRequests extends ListRecords
{
    protected static string $resource = RefundRequestResource::class;

    public function getTitle(): string
    {
        return 'Mes Remboursements';
    }
}