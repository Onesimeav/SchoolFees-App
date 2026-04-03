<?php

namespace App\Filament\Staff\Resources\Fees\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InstallmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'installments';

    protected static ?string $title = 'Installments';

    protected static ?string $modelLabel = 'installment';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('number')
                    ->label('Installment Number')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->default(fn () => $this->getOwnerRecord()->installments()->count() + 1)
                    ->helperText('Sequential number for this installment (e.g., 1, 2, 3)'),
                TextInput::make('amount')
                    ->label('Installment Amount')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0.01)
                    ->step(0.01)
                    ->helperText(fn () =>
                        'Total fee amount: $' . number_format($this->getOwnerRecord()->total_amount, 2)
                    ),
                DatePicker::make('due_date')
                    ->label('Due Date')
                    ->required()
                    ->native(false)
                    ->helperText('When is this installment payment due?'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('number')
            ->columns([
                TextColumn::make('number')
                    ->label('Installment #')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable()
                    ->description(fn ($record) =>
                        $record->due_date < now() ? 'Overdue' :
                        ($record->due_date < now()->addDays(7) ? 'Due soon' : '')
                    )
                    ->color(fn ($record) =>
                        $record->due_date < now() ? 'danger' :
                        ($record->due_date < now()->addDays(7) ? 'warning' : 'gray')
                    ),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Installment'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('number', 'asc')
            ->emptyStateHeading('No installments yet')
            ->emptyStateDescription('Create installments to split this tuition fee into payments.')
            ->emptyStateIcon('heroicon-o-banknotes');
    }
}
