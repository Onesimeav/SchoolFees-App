<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;

class AdminRevenueChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Revenus mensuels';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $labels   = [];
        $revenues = [];

        $shortMonths = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $y    = (int) $date->format('Y');
            $m    = (int) $date->format('n');

            $labels[]   = $shortMonths[$m - 1] . ' ' . $date->format('y');
            $revenues[] = (float) Transaction::where('status', 'completed')
                ->whereYear('date', $y)
                ->whereMonth('date', $m)
                ->sum('amount');
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Revenus (F CFA)',
                    'data'            => $revenues,
                    'borderColor'     => 'rgb(34,197,94)',
                    'backgroundColor' => 'rgba(34,197,94,0.1)',
                    'tension'         => 0.4,
                    'fill'            => true,
                ],
            ],
            'labels' => $labels,
        ];
    }
}