<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerTasksResource\Pages;
use App\Filament\Resources\CustomerTasksResource\RelationManagers;
use App\Models\CustomerTasks;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Customer;
use App\Models\User;

class CustomerTasksResource extends Resource
{
    protected static ?string $title = 'Tâches Clientes';
 
    protected static ?string $navigationLabel = 'Tâches Clientes';

    protected static ?string $modelLabel = 'Tâches Clientes';

    protected static ?string $model = CustomerTasks::class;

    protected static ?string $navigationGroup = 'CRM Clients';

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->Orwhere('status', 'in_progress')->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                    Forms\Components\Card::make()
                        ->schema([
                            Forms\Components\Select::make('attributedto')
                                ->label('Administrateur attribué')
                                ->searchable()
                                ->getSearchResultsUsing(fn (string $query) => User::where('name', 'like', "%{$query}%")->orWhere('email', 'like', "%{$query}%")->pluck('name', 'id'))
                                ->getOptionLabelUsing(fn ($value): ?string => User::find($value)?->name)
                                ->required()->columnSpan(['sm' => 2]),
                            Forms\Components\Select::make('customerid')
                                ->label('Client')
                                ->searchable()
                                ->getSearchResultsUsing(fn (string $query) => Customer::where('name', 'like', "%{$query}%")->orWhere('email', 'like', "%{$query}%")->pluck('name', 'id'))
                                ->getOptionLabelUsing(fn ($value): ?string => Customer::find($value)?->name)
                                ->required()->columnSpan(['sm' => 2]),
                        ])->columns([
                            'sm' => 4,
                        ]),
                    Forms\Components\Card::make()
                        ->schema([
                            Forms\Components\MarkdownEditor::make('content')->required(),
                        ]),
                    ])->columnSpan([
                        'sm' => 2,
                    ]),
                Forms\Components\Group::make()
                    ->schema([
                    Forms\Components\Card::make()
                        ->schema([
                            Forms\Components\Radio::make('status')
                                ->label('Statut')
                                ->required()
                                ->options([
                                    'ended' => 'Terminée',
                                    'in_progress' => 'En cours',
                                    'pending' => 'En attente'
                                ])
                                ->descriptions([
                                    'ended' => 'La tâche est terminée.',
                                    'in_progress' => 'La tâche est en cours.',
                                    'pending' => 'La tâche n\'a pas commencé.',
                                ])
                        ])->columnSpan(1),
                    Forms\Components\Card::make()
                        ->schema([
                            Forms\Components\Placeholder::make('created_at')
                                ->label('Créé')
                                ->content(fn (?CustomerTasks $record): string => $record ? $record->created_at->diffForHumans() : '-'),
                            Forms\Components\Placeholder::make('updated_at')
                                ->label('Dernière modification')
                                ->content(fn (?CustomerTasks $record): string => $record ? $record->updated_at->diffForHumans() : '-'),
                        ])->columnSpan(1),
                ]),
            ])->columns([
                'sm' => 3,
                'lg' => null,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customerid')
                    ->label('Client')
                    ->getStateUsing(fn ($record): ?string => Customer::find($record->customerid)?->name ?? null)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('attributedto')
                    ->label('Attribué à')
                    ->getStateUsing(fn ($record): ?string => User::find($record->attributedto)?->name ?? null)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->enum([
                        'ended' => 'Terminée',
                        'in_progress' => 'En cours',
                        'pending' => 'Non visualisé',
                    ])
                    ->colors([
                        'danger' => 'pending',
                        'primary' => 'in_progress',
                        'success' => 'ended',
                    ])
                    ->searchable()
                    ->sortable(),
            ])->defaultSort('status', 'desc')->defaultSort('created_at', 'desc')
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
            'index' => Pages\ListCustomerTasks::route('/'),
            'create' => Pages\CreateCustomerTasks::route('/create'),
            'edit' => Pages\EditCustomerTasks::route('/{record}/edit'),
        ];
    }    
}
