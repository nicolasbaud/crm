<?php

namespace App\Filament\Resources\Development\CalendarResource\Pages;

use App\Filament\Resources\Development\CalendarResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCalendar extends EditRecord
{
    protected static string $resource = CalendarResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
