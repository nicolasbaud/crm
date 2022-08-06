<?php

namespace App\Filament\Resources\ChatResource\Pages;

use App\Filament\Resources\ChatResource;
use Filament\Forms;
use Illuminate\Contracts\View\View;
use Filament\Resources\Pages\ListRecords;

class ListChats extends ListRecords implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms; 

    protected static string $view = 'chat.box';

    protected static string $resource = ChatResource::class;

    public $message = '';
 
    protected function getFormSchema(): array 
    {
        return [
            Forms\Components\TextInput::make('message')
                ->placeholder('Entez votre message ici...')
                ->required(),
        ];
    }

    public function submit()
    {
        \App\Models\Chat::create([
            'userid' => auth()->user()->id,
            'message' => $this->message,
        ]);

        $this->reset('message');
    }
}
