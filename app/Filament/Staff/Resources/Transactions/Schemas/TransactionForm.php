<?php

namespace App\Filament\Staff\Resources\Transactions\Schemas;

use App\Models\Fee;
use App\Models\TuitionFee;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Transaction Details')
                    ->schema([
                        Select::make('user_id')
                            ->label('Student')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->relationship(
                                'user',
                                'email',
                                fn ($query) => $query->whereHas('roles', fn ($q) => $q->where('name', 'parent_student'))
                            )
                            ->getOptionLabelFromRecordUsing(fn (User $record) => $record->full_name . ' (' . $record->email . ')')
                            ->helperText('Select the student making this payment'),
                        Select::make('fee_id')
                            ->label('Fee')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->relationship('fee', 'title')
                            ->getOptionLabelFromRecordUsing(fn (Fee $record) => $record->title . ' - $' . number_format($record->total_amount, 2))
                            ->helperText('Select the fee being paid'),
                        Select::make('installment_id')
                            ->label('Installment')
                            ->searchable()
                            ->options(function (Get $get) {
                                if (!$get('fee_id')) {
                                    return [];
                                }
                                $fee = Fee::find($get('fee_id'));
                                if (!$fee || !$fee->installments) {
                                    return [];
                                }
                                return $fee->installments->pluck('number', 'id')->map(function ($num) {
                                    return 'Installment #' . $num;
                                })->toArray();
                            })
                            ->visible(function (Get $get) {
                                if (!$get('fee_id')) {
                                    return false;
                                }
                                $fee = Fee::find($get('fee_id'));
                                return $fee && $fee->type === 'App\\Models\\TuitionFee';
                            })
                            ->helperText('Select which installment this payment is for (optional)'),
                    ])
                    ->columns(2),

                Section::make('Payment Information')
                    ->schema([
                        TextInput::make('amount')
                            ->label('Amount')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0.01)
                            ->step(0.01)
                            ->helperText(function (Get $get) {
                                $fee = $get('fee_id') ? Fee::find($get('fee_id')) : null;
                                return $fee ? 'Fee total: $' . number_format($fee->total_amount, 2) : '';
                            }),
                        DatePicker::make('date')
                            ->label('Payment Date')
                            ->required()
                            ->native(false)
                            ->default(now())
                            ->maxDate(now()),
                        Select::make('status')
                            ->label('Payment Status')
                            ->required()
                            ->options([
                                'pending' => 'Pending',
                                'completed' => 'Completed',
                                'failed' => 'Failed',
                                'refunded' => 'Refunded',
                            ])
                            ->default('pending')
                            ->helperText('Current status of this payment'),
                    ])
                    ->columns(3),

                Section::make('Mobile Money Payment')
                    ->schema([
                        TextInput::make('phone_number')
                            ->label('Phone Number')
                            ->tel()
                            ->required()
                            ->maxLength(20)
                            ->helperText('Mobile money phone number used for payment'),
                        TextInput::make('kkiapay_reference')
                            ->label('Payment Reference')
                            ->maxLength(255)
                            ->helperText('Transaction reference from Kkiapay payment gateway'),
                    ])
                    ->columns(2)
                    ->description('All payments are processed via mobile money'),
            ]);
    }
}
