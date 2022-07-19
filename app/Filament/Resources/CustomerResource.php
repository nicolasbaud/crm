<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Squire\Models\Country;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction,AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;

class CustomerResource extends Resource
{
    protected static ?string $title = 'Clients';
 
    protected static ?string $navigationLabel = 'Clients';

    protected static ?string $modelLabel = 'Clients';

    protected static ?string $model = Customer::class;

    protected static ?string $navigationGroup = 'CRM Clients';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                    Forms\Components\Card::make()
                        ->schema([
                            Forms\Components\TextInput::make('name')->label('Nom complet')->required()->columnSpan(['sm' => 2]),
                            Forms\Components\TextInput::make('email')->email()->unique(Customer::class, 'email', fn ($record) => $record)->required()->columnSpan(['sm' => 2]),
                            Forms\Components\DatePicker::make('birthday')->label('Date de naissance'),
                            Forms\Components\Select::make('gender')
                                ->label('Genre')
                                ->searchable()
                                ->options([
                                    'male' => 'Mr',
                                    'female' => 'Mme',
                                ]),
                            Forms\Components\TextInput::make('phone')->label('Téléphone')->required()->columnSpan(['sm' => 2]),
                        ])->columns([
                            'sm' => 4,
                        ]),
                    Forms\Components\Card::make()
                        ->schema([
                            Forms\Components\TextInput::make('street')->label('Adresse')->required()->columnSpan(['sm' => 12]),
                            Forms\Components\TextInput::make('city')->label('Ville')->required()->columnSpan(['sm' => 4]),
                            Forms\Components\TextInput::make('state')->label('Région')->required()->columnSpan(['sm' => 4]),
                            Forms\Components\TextInput::make('zip')->label('Code Postal')->required()->columnSpan(['sm' => 4]),
                            Forms\Components\Select::make('country')
                                ->label('Pays')
                                ->required()
                                ->searchable()
                                ->getSearchResultsUsing(fn (string $query) => Country::where('name', 'like', "%{$query}%")->pluck('name', 'id'))
                                ->getOptionLabelUsing(fn ($value): ?string => Country::find($value)?->name)->columnSpan(['sm' => 12]),
                        ])->columns([
                            'sm' => 12,
                        ]),
                    Forms\Components\Card::make()
                        ->schema([
                            Forms\Components\RichEditor::make('notes')->columnSpan(['sm' => 12]),
                        ]),
                    ])->columnSpan([
                        'sm' => 2,
                    ]),
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Créé')
                            ->content(fn (?Customer $record): string => $record ? $record->created_at->diffForHumans() : '-'),
                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Dernière modification')
                            ->content(fn (?Customer $record): string => $record ? $record->updated_at->diffForHumans() : '-'),
                    ])
                    ->columnSpan(1),
            ])->columns([
                'sm' => 3,
                'lg' => null,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Adresse e-mail')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Téléphone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country')
                    ->label('Pays')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé')
                    ->since()
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                FilamentExportBulkAction::make('export'),
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export'),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }    
}
