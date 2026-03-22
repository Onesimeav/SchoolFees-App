<?php

namespace App\Filament\Widgets;

use App\Models\ClassRegistration;
use App\Models\RefundRequest;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $revenue = Transaction::where('status', 'completed')
            ->whereYear('date', now()->year)
            ->sum('amount');

        return [
            Stat::make('Revenus (' . now()->year . ')', number_format($revenue, 0, ',', ' ') . ' F CFA')
                ->description('Transactions complétées cette année')
                ->color('success')
                ->icon('heroicon-o-banknotes'),

            Stat::make('Transactions en attente', Transaction::where('status', 'pending')->count())
                ->description('À traiter')
                ->color('warning')
                ->icon('heroicon-o-clock'),

            Stat::make('Transactions complétées', Transaction::where('status', 'completed')->count())
                ->description('Total')
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Stat::make('Élèves inscrits', ClassRegistration::where('status', 'accepted')->count())
                ->description('Inscriptions acceptées')
                ->color('info')
                ->icon('heroicon-o-academic-cap'),

            Stat::make('Inscriptions en attente', ClassRegistration::where('status', 'pending')->count())
                ->description('En attente de validation')
                ->color('warning')
                ->icon('heroicon-o-user-plus'),

            Stat::make('Remboursements en attente', RefundRequest::where('status', 'pending')->count())
                ->description('Demandes à traiter')
                ->color('danger')
                ->icon('heroicon-o-arrow-uturn-left'),
        ];
    }
}