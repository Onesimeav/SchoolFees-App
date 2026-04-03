<?php

namespace App\Filament\Widgets;

use App\Models\Grade;
use Filament\Widgets\ChartWidget;

class AdminRegistrationsChartWidget extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Inscriptions par classe';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $grades = Grade::withCount([
            'classRegistrations as accepted_count' => fn ($q) => $q->where('status', 'accepted'),
            'classRegistrations as pending_count'  => fn ($q) => $q->where('status', 'pending'),
        ])->orderBy('name')->get();

        return [
            'datasets' => [
                [
                    'label'           => 'Acceptées',
                    'data'            => $grades->pluck('accepted_count')->toArray(),
                    'backgroundColor' => 'rgb(59,130,246)',
                ],
                [
                    'label'           => 'En attente',
                    'data'            => $grades->pluck('pending_count')->toArray(),
                    'backgroundColor' => 'rgb(249,115,22)',
                ],
            ],
            'labels' => $grades->pluck('name')->toArray(),
        ];
    }
}