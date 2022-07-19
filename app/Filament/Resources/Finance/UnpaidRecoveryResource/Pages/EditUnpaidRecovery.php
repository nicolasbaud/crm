<?php

namespace App\Filament\Resources\Finance\UnpaidRecoveryResource\Pages;

use App\Filament\Resources\Finance\UnpaidRecoveryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnpaidRecovery extends EditRecord
{
    protected static string $resource = UnpaidRecoveryResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
