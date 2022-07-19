<?php

namespace App\Filament\Widgets;

use Filament\Widgets\LineChartWidget;
use App\Models\Customer;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class CustomerChart extends LineChartWidget
{
    protected static ?string $heading = 'Clients';

    protected static ?int $sort = 2;    

    protected function getData(): array
    {
        $data = Trend::model(Customer::class)
            ->between(
                start: now()->startOfMonth()->subYear(1),
                end: now()->endOfMonth(),
            )
            ->perMonth()
            ->count();
        return [
            'datasets' => [
                [
                    'label' => 'Clients',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => 'rgb(234, 179, 8)',
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }
}
