<?php

namespace App\Filament\Resources\Development\CalendarResource\Pages;

use App\Filament\Resources\Development\CalendarResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCalendars extends ListRecords
{
    protected static string $resource = CalendarResource::class;
 
    protected function getHeaderWidgets(): array
    {
        return [
            CalendarResource\Widgets\CalendarWidget::class,
        ];
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
