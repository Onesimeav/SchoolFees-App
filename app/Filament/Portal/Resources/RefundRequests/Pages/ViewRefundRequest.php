<?php

namespace App\Filament\Portal\Resources\RefundRequests\Pages;

use App\Filament\Portal\Resources\RefundRequests\RefundRequestResource;
use Filament\Resources\Pages\ViewRecord;

class ViewRefundRequest extends ViewRecord
{
    protected static string $resource = RefundRequestResource::class;

    public function getTitle(): string
    {
        return 'Détails de la demande';
    }
}