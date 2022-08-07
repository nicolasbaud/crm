<?php

namespace App\Filament\Resources\Development\CalendarResource\Widgets;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Filament\Forms;
use App\Models\DevelopmentCalendar;

class CalendarWidget extends FullCalendarWidget
{
    public function fetchEvents(array $fetchInfo): array
    {
    $appointments = DevelopmentCalendar::query()
        ->where([
            ['start', '>=', $fetchInfo['start']],
            ['end', '<=', $fetchInfo['end']],
        ])
        ->get();

    return $appointments->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'title' => $appointment->title,
                'start' => $appointment->start,
                'end' => $appointment->end,
            ];
        })
        ->toArray();
    }

    public function createEvent(array $data): void
    {
        DevelopmentCalendar::create($data);
        $this->refreshEvents();
    }

    public function editEvent(array $data): void
    {
        DevelopmentCalendar::find($this->event);
        $this->event->update($data);
        $this->refreshEvents();
    }

    public function resolveEventRecord(array $data)
    {
        // Using Appointment class as example
        return DevelopmentCalendar::find($data['id']);
    }

    protected static function getEditEventFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('title')
                ->label('Titre')
                ->required(),
            Forms\Components\DatePicker::make('start')
                ->label('Début')
                ->required(),
            Forms\Components\DatePicker::make('end')
                ->label('Fin')
                ->default(null),
        ];
    }
}