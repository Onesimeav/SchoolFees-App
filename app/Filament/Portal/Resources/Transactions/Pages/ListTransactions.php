<?php

namespace App\Filament\Portal\Resources\Transactions\Pages;

use App\Filament\Portal\Resources\Transactions\TransactionResource;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    public function getTitle(): string
    {
        return 'Mes Transactions';
    }
}