<?php

namespace App\Filament\Resources\Development;

use App\Filament\Resources\Development\CalendarResource\Pages;
use App\Filament\Resources\Development\CalendarResource\RelationManagers;
use App\Models\DevelopmentCalendar;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CalendarResource extends Resource
{
    protected static ?string $title = 'Calendrier';
 
    protected static ?string $navigationLabel = 'Calendrier';

    protected static ?string $modelLabel = 'Calendrier';

    protected static ?string $model = DevelopmentCalendar::class;

    protected static ?string $navigationGroup = 'Développeur';

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\Card::make()
                ->schema([
                    Forms\Components\TextInput::make('title')->label('Titre')
                        ->required()
                        ->columnSpan(12),
                    Forms\Components\DatePicker::make('start')
                        ->label('Date de début')
                        ->displayFormat('d F Y')
                        ->required()
                        ->columnSpan(6),
                    Forms\Components\DatePicker::make('end')
                        ->label('Date de fin')
                        ->displayFormat('d F Y')
                        ->required()
                        ->columnSpan(6),
                ])->columns(12),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start')
                    ->label('Date de début')
                    ->date('d F Y')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end')
                    ->label('Date de fin')
                    ->date('d F Y')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCalendars::route('/'),
            'create' => Pages\CreateCalendar::route('/create'),
            'edit' => Pages\EditCalendar::route('/{record}/edit'),
        ];
    }    
}
