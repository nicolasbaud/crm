<?php

namespace App\Filament\Resources\CustomerTasksResource\Pages;

use App\Filament\Resources\CustomerTasksResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerTasks extends CreateRecord
{
    protected static string $resource = CustomerTasksResource::class;
}
