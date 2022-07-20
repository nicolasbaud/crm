<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Customer;
use App\Models\UnpaidRecovery;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getCards(): array
    {
        return [
            Card::make('Clients', Customer::count()),
            Card::make('Recouvrement', UnpaidRecovery::where('status', '!=', 'ended')->count()),
        ];
    }
}