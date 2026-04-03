<?php

namespace App\Filament\Staff\Widgets;

use App\Filament\Staff\Resources\ClassRegistrations\ClassRegistrationResource;
use App\Models\ClassRegistration;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class StaffRecentRegistrationsWidget extends TableWidget
{
    protected static ?int $sort = 5;

    protected static ?string $heading = 'Dernières demandes d\'inscription';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->check();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ClassRegistration::query()->latest()->limit(6))
            ->paginated(false)
            ->recordUrl(fn ($record) => ClassRegistrationResource::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('user.full_name')
                    ->label('Élève')
                    ->formatStateUsing(fn ($record) => $record->user?->name . ' ' . $record->user?->surname),

                TextColumn::make('grade.name')
                    ->label('Classe')
                    ->badge()
                    ->color('info'),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending'  => 'En attente',
                        'accepted' => 'Accepté',
                        'refused'  => 'Refusé',
                        default    => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'pending'  => 'warning',
                        'accepted' => 'success',
                        'refused'  => 'danger',
                        default    => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Soumis le')
                    ->date('d/m/Y'),
            ]);
    }
}
