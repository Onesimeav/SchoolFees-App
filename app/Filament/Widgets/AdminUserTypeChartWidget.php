<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class AdminUserTypeChartWidget extends ChartWidget
{
    protected static ?int $sort = 4;

    protected ?string $heading = 'Répartition des comptes';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $roles = [
            'admin'          => 'Administrateur',
            'accountant'     => 'Comptable',
            'secretary'      => 'Secrétaire',
            'employee'       => 'Employé',
            'parent_student' => 'Parent / Élève',
        ];

        $counts = [];
        foreach (array_keys($roles) as $roleName) {
            $counts[] = User::role($roleName)->count();
        }

        return [
            'datasets' => [
                [
                    'data'            => $counts,
                    'backgroundColor' => ['#DC2626', '#D97706', '#2563EB', '#6B7280', '#16A34A'],
                ],
            ],
            'labels' => array_values($roles),
        ];
    }
}
