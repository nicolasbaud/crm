<?php

namespace App\Filament\Widgets;

use Filament\Widgets\LineChartWidget;
use App\Models\UnpaidRecovery;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class UnpaidRecoveryChart extends LineChartWidget
{
    protected static ?string $heading = 'Recouvrement';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = Trend::model(UnpaidRecovery::class)
            ->between(
                start: now()->startOfMonth()->subYear(1),
                end: now()->endOfMonth(),
            )
            ->perMonth()
            ->count();
        return [
            'datasets' => [
                [
                    'label' => 'Recouvrement',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => 'rgb(234, 179, 8)',
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }
}
