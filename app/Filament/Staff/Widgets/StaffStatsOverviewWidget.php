<?php

namespace App\Filament\Staff\Widgets;

use App\Models\ClassRegistration;
use App\Models\Fee;
use App\Models\Grade;
use App\Models\Installment;
use App\Models\RefundRequest;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StaffStatsOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth()->check();
    }

    protected function getStats(): array
    {
        $user  = auth()->user();
        $stats = [];

        if ($user->hasRole('accountant')) {
            $revenue = Transaction::where('status', 'completed')
                ->whereYear('date', now()->year)
                ->sum('amount');

            $stats[] = Stat::make('Revenus (' . now()->year . ')', number_format($revenue, 0, ',', ' ') . ' F CFA')
                ->description('Transactions complétées cette année')
                ->color('success')
                ->icon('heroicon-o-banknotes');

            $stats[] = Stat::make('Transactions en attente', Transaction::where('status', 'pending')->count())
                ->description('À traiter')
                ->color('warning')
                ->icon('heroicon-o-clock');

            $stats[] = Stat::make('Remboursements en attente', RefundRequest::where('status', 'pending')->count())
                ->description('Demandes à traiter')
                ->color('danger')
                ->icon('heroicon-o-arrow-uturn-left');

            $refundedTotal = Transaction::where('status', 'refunded')->sum('amount');
            $stats[] = Stat::make('Remboursements traités', number_format($refundedTotal, 0, ',', ' ') . ' F CFA')
                ->description('Total des montants remboursés')
                ->color('info')
                ->icon('heroicon-o-arrow-uturn-left');

            $stats[] = Stat::make('Paiements dans les délais', $this->computeOnTimePaymentRate() . ' %')
                ->description('Frais obligatoires et versements payés à temps')
                ->color('success')
                ->icon('heroicon-o-chart-bar');
        }

        if ($user->hasRole('secretary')) {
            $stats[] = Stat::make('Inscriptions en attente', ClassRegistration::where('status', 'pending')->count())
                ->description('En attente de validation')
                ->color('warning')
                ->icon('heroicon-o-user-plus');

            $stats[] = Stat::make('Élèves inscrits', ClassRegistration::where('status', 'accepted')->count())
                ->description('Inscriptions acceptées')
                ->color('info')
                ->icon('heroicon-o-academic-cap');

            $stats[] = Stat::make('Frais actifs', Fee::whereNull('deleted_at')->count())
                ->description('Frais en cours')
                ->color('primary')
                ->icon('heroicon-o-rectangle-stack');
        }

        if ($user->hasRole('employee')) {
            $stats[] = Stat::make('Élèves inscrits', ClassRegistration::where('status', 'accepted')->count())
                ->description('Inscriptions acceptées')
                ->color('info')
                ->icon('heroicon-o-academic-cap');

            $stats[] = Stat::make('Classes', Grade::count())
                ->description('Niveaux disponibles')
                ->color('primary')
                ->icon('heroicon-o-building-library');
        }

        return $stats;
    }

    private function computeOnTimePaymentRate(): int
    {
        $acceptedUserIds = ClassRegistration::where('status', 'accepted')
            ->pluck('user_id')->unique();
        $total = $acceptedUserIds->count();
        if ($total === 0) {
            return 100;
        }

        $overdueGeneralFees = Fee::where('type', 'App\Models\GeneralFee')
            ->where('required', true)
            ->where('due_before', '<', today())
            ->get();

        $overdueInstallments = Installment::where('due_date', '<', today())->get();

        $studentsWithOverdue = 0;
        foreach ($acceptedUserIds as $uid) {
            $overdue = false;
            foreach ($overdueGeneralFees as $fee) {
                $paid = Transaction::where('user_id', $uid)
                    ->where('fee_id', $fee->id)
                    ->where('status', 'completed')
                    ->where('date', '<=', $fee->due_before)
                    ->exists();
                if (! $paid) {
                    $overdue = true;
                    break;
                }
            }
            if (! $overdue) {
                foreach ($overdueInstallments as $inst) {
                    $paid = Transaction::where('user_id', $uid)
                        ->where('installment_id', $inst->id)
                        ->where('status', 'completed')
                        ->where('date', '<=', $inst->due_date)
                        ->exists();
                    if (! $paid) {
                        $overdue = true;
                        break;
                    }
                }
            }
            if ($overdue) {
                $studentsWithOverdue++;
            }
        }

        return round(($total - $studentsWithOverdue) / $total * 100);
    }
}