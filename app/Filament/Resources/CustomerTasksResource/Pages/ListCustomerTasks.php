<?php

namespace App\Filament\Resources\CustomerTasksResource\Pages;

use App\Filament\Resources\CustomerTasksResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomerTasks extends ListRecords
{
    protected static string $resource = CustomerTasksResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
