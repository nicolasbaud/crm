<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChatResource\Pages;
use App\Filament\Resources\ChatResource\RelationManagers;
use App\Models\Chat;
use Filament\Resources\Resource;

class ChatResource extends Resource
{
    protected static ?string $title = 'Chat';
 
    protected static ?string $navigationLabel = 'Chat';

    protected static ?string $modelLabel = 'Chat';

    protected static ?string $model = Chat::class;

    protected static ?string $navigationGroup = 'Développeur';

    protected static ?string $navigationIcon = 'heroicon-o-chat';
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChats::route('/'),
        ];
    }
}
