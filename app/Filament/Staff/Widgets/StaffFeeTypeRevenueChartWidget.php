<?php

namespace App\Filament\Staff\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;

class StaffFeeTypeRevenueChartWidget extends ChartWidget
{
    protected static ?int $sort = 4;

    protected ?string $heading = 'Revenus par type de frais';

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->hasRole('accountant');
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $types = [
            'App\Models\RegistrationFee' => 'Inscription',
            'App\Models\TuitionFee'      => 'Scolarité',
            'App\Models\GeneralFee'      => 'Frais généraux',
        ];

        $revenue = [];
        foreach (array_keys($types) as $type) {
            $revenue[] = (float) Transaction::where('status', 'completed')
                ->whereHas('fee', fn ($q) => $q->where('type', $type))
                ->sum('amount');
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Revenus (F CFA)',
                    'data'            => $revenue,
                    'backgroundColor' => ['#6366F1', '#22C55E', '#F97316'],
                ],
            ],
            'labels' => array_values($types),
        ];
    }
}
