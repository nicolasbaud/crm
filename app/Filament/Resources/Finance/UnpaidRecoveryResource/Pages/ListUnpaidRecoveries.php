<?php

namespace App\Filament\Resources\Finance\UnpaidRecoveryResource\Pages;

use App\Filament\Resources\Finance\UnpaidRecoveryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUnpaidRecoveries extends ListRecords
{
    protected static string $resource = UnpaidRecoveryResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
