<?php

namespace App\Filament\Widgets;

use Filament\Widgets\LineChartWidget;
use App\Models\CustomerTasks;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class CustomerTasksChart extends LineChartWidget
{
    protected static ?string $heading = 'Tâches Clientes';

    protected static ?int $sort = 2;    

    protected function getData(): array
    {
        $data = Trend::model(CustomerTasks::class)
            ->between(
                start: now()->startOfMonth()->subYear(1),
                end: now()->endOfMonth(),
            )
            ->perMonth()
            ->count();
        return [
            'datasets' => [
                [
                    'label' => 'Tâches Clientes',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => 'rgb(234, 179, 8)',
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }
}
