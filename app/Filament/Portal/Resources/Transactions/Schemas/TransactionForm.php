<?php

namespace App\Filament\Portal\Resources\Transactions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required(),
                TextInput::make('fee_id'),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                DatePicker::make('date')
                    ->required(),
                TextInput::make('status')
                    ->required(),
                TextInput::make('kkiapay_reference'),
                TextInput::make('phone_number')
                    ->tel()
                    ->required(),
                TextInput::make('installment_id'),
            ]);
    }
}
