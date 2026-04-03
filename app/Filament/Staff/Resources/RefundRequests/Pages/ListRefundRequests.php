<?php

namespace App\Filament\Staff\Resources\RefundRequests\Pages;

use App\Filament\Staff\Resources\RefundRequests\RefundRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListRefundRequests extends ListRecords
{
    protected static string $resource = RefundRequestResource::class;
}