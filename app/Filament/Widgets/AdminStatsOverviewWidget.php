<?php

namespace App\Filament\Widgets;

use App\Models\ClassRegistration;
use App\Models\Fee;
use App\Models\RefundRequest;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    private function computeOnTimePaymentRate(): int
    {
        $acceptedUserIds = ClassRegistration::where('status', 'accepted')->pluck('user_id')->unique();
        $total = $acceptedUserIds->count();

        if ($total === 0) {
            return 100;
        }

        $overdueGeneralFees = Fee::where('type', 'App\Models\GeneralFee')
            ->where('required', true)
            ->where('due_before', '<', today())
            ->get();

        if ($overdueGeneralFees->isEmpty()) {
            return 100;
        }

        $studentsWithOverdue = 0;
        foreach ($acceptedUserIds as $uid) {
            foreach ($overdueGeneralFees as $fee) {
                $paid = Transaction::where('user_id', $uid)
                    ->where('fee_id', $fee->id)
                    ->where('status', 'completed')
                    ->where('date', '<=', $fee->due_before)
                    ->exists();
                if (! $paid) {
                    $studentsWithOverdue++;
                    break;
                }
            }
        }

        return (int) round(($total - $studentsWithOverdue) / $total * 100);
    }

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

            Stat::make('Frais disponibles', Fee::count())
                ->description('Frais créés dans le système')
                ->color('primary')
                ->icon('heroicon-o-rectangle-stack'),

            Stat::make('Remboursements traités', number_format(Transaction::where('status', 'refunded')->sum('amount'), 0, ',', ' ') . ' F CFA')
                ->description('Total des montants remboursés')
                ->color('info')
                ->icon('heroicon-o-arrow-uturn-left'),

            Stat::make('Paiement dans les délais', $this->computeOnTimePaymentRate() . ' %')
                ->description('Frais obligatoires payés à temps')
                ->color('success')
                ->icon('heroicon-o-chart-bar'),
        ];
    }
}