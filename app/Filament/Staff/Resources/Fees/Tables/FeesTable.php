<?php

namespace App\Filament\Staff\Resources\Fees\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class FeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Fee Type')
                    ->badge()
                    ->colors([
                        'info' => 'App\\Models\\RegistrationFee',
                        'success' => 'App\\Models\\TuitionFee',
                        'warning' => 'App\\Models\\GeneralFee',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'App\\Models\\RegistrationFee' => 'Registration',
                        'App\\Models\\TuitionFee' => 'Tuition',
                        'App\\Models\\GeneralFee' => 'General',
                        default => $state,
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                TextColumn::make('classroom')
                    ->label('Classroom')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('All'),
                TextColumn::make('academic_year')
                    ->label('Academic Year')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label('Amount')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('number_of_installments')
                    ->label('Installments')
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->placeholder('-')
                    ->tooltip(fn ($record) => $record->type === 'App\\Models\\TuitionFee' ?
                        'Tuition fee split into ' . ($record->number_of_installments ?? 0) . ' installments' :
                        'Not applicable'),
                IconColumn::make('required')
                    ->label('Required')
                    ->boolean()
                    ->toggleable()
                    ->tooltip(fn ($record) => $record->type === 'App\\Models\\GeneralFee' ?
                        ($record->required ? 'This is a required fee' : 'This fee is optional') :
                        'Not applicable'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Fee Type')
                    ->options([
                        'App\\Models\\RegistrationFee' => 'Registration Fee',
                        'App\\Models\\TuitionFee' => 'Tuition Fee',
                        'App\\Models\\GeneralFee' => 'General Fee',
                    ]),
                SelectFilter::make('academic_year')
                    ->label('Academic Year')
                    ->options(fn () => \App\Models\Fee::query()
                        ->distinct()
                        ->pluck('academic_year', 'academic_year')
                        ->toArray()
                    ),
                SelectFilter::make('classroom')
                    ->label('Classroom')
                    ->options(fn () => \App\Models\Fee::query()
                        ->distinct()
                        ->whereNotNull('classroom')
                        ->pluck('classroom', 'classroom')
                        ->toArray()
                    ),
                TrashedFilter::make(),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
