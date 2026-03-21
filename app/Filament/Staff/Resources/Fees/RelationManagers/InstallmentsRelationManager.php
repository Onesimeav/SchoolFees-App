<?php

namespace App\Filament\Staff\Resources\Fees\RelationManagers;

use Closure;
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

    protected static ?string $title = 'Versements';

    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->type === 'App\Models\TuitionFee';
    }

    protected static ?string $modelLabel = 'versement';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('amount')
                    ->label('Montant du versement')
                    ->required()
                    ->numeric()
                    ->suffix('F CFA')
                    ->minValue(1)
                    ->step(1)
                    ->helperText(fn () =>
                        'Montant total des frais : ' . number_format($this->getOwnerRecord()->total_amount, 0, ',', ' ') . ' F CFA'
                    )
                    ->rules([
                        fn ($livewire): Closure => function (string $attribute, mixed $value, Closure $fail) use ($livewire): void {
                            $fee           = $livewire->getOwnerRecord();
                            $editingRecord = $livewire->getMountedTableActionRecord();
                            $existingSum   = (float) $fee->installments()
                                ->when($editingRecord, fn ($q) => $q->where('id', '!=', $editingRecord->id))
                                ->sum('amount');
                            $available     = max(0.0, (float) $fee->total_amount - $existingSum);

                            if ((float) $value > $available) {
                                $fail(
                                    'Montant dépassé. Disponible pour ce versement : '
                                    . number_format($available, 0, ',', ' ') . ' F CFA '
                                    . '(total : ' . number_format((float) $fee->total_amount, 0, ',', ' ') . ' F CFA).'
                                );
                            }
                        },
                    ]),
                DatePicker::make('due_date')
                    ->label('Date d\'échéance')
                    ->required()
                    ->native(false)
                    ->helperText('Date limite de paiement pour ce versement')
                    ->rules([
                        fn ($livewire): Closure => function (string $attribute, mixed $value, Closure $fail) use ($livewire): void {
                            $fee           = $livewire->getOwnerRecord();
                            $editingRecord = $livewire->getMountedTableActionRecord();

                            if ($fee->installments()
                                ->when($editingRecord, fn ($q) => $q->where('id', '!=', $editingRecord->id))
                                ->where('due_date', $value)
                                ->exists()
                            ) {
                                $fail('Un versement existe déjà pour cette date d\'échéance.');
                            }
                        },
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('number')
            ->defaultSort('due_date', 'asc')
            ->columns([
                TextColumn::make('number')
                    ->label('N°')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('amount')
                    ->label('Montant')
                    ->money('XOF')
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Échéance')
                    ->date('d/m/Y')
                    ->sortable()
                    ->description(fn ($record) =>
                        $record->due_date < now() ? 'En retard' :
                        ($record->due_date < now()->addDays(7) ? 'Échéance proche' : '')
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
                    ->label('Ajouter un versement'),
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
            ->emptyStateHeading('Aucun versement')
            ->emptyStateDescription('Ajoutez des versements pour répartir ces frais de scolarité en plusieurs paiements.')
            ->emptyStateIcon('heroicon-o-banknotes');
    }
}
