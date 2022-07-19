<?php

namespace App\Filament\Resources\Finance;

use App\Filament\Resources\Finance\UnpaidRecoveryResource\Pages;
use App\Filament\Resources\Finance\UnpaidRecoveryResource\RelationManagers;
use App\Models\UnpaidRecovery;
use App\Models\Customer;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class UnpaidRecoveryResource extends Resource
{
    protected static ?string $title = 'Recouvrement';
 
    protected static ?string $navigationLabel = 'Recouvrement';

    protected static ?string $modelLabel = 'Recouvrement';

    protected static ?string $model = UnpaidRecovery::class;

    protected static ?string $navigationGroup = 'Finance';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                    Forms\Components\Card::make()
                        ->schema([
                            Forms\Components\TextInput::make('ref')->label('Votre référence')->required()->columnSpan(['sm' => 2]),
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
                            Forms\Components\TextInput::make('amount')->label('Montant')->required()->columnSpan(['sm' => 4]),
                            Forms\Components\DateTimePicker::make('factured_at')->label('Date de facturation')->required()->columnSpan(['sm' => 4]),
                            Forms\Components\DateTimePicker::make('echance_at')->label('Date d\'échéance')->required()->columnSpan(['sm' => 4]),
                        ])->columns([
                            'sm' => 12,
                        ]),
                    Forms\Components\Card::make()
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('attachment')->collection('unpaid-recovery')->label('')
                                ->removeUploadedFileButtonPosition('right')
                                ->uploadButtonPosition('right')
                                ->uploadProgressIndicatorPosition('center')
                                ->enableOpen()
                                ->enableDownload(),
                        ]),
                    Forms\Components\Card::make()
                        ->schema([
                            Forms\Components\RichEditor::make('notes')->columnSpan(['sm' => 12]),
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
                                    'ended' => 'Le recouvrement est terminée.',
                                    'in_progress' => 'La recouvrement est en cours.',
                                    'pending' => 'La recouvrement n\'a pas commencé.',
                                ])
                        ])->columnSpan(1),
                    Forms\Components\Card::make()
                        ->schema([
                            Forms\Components\Select::make('process')
                                ->label('Actions réalisées')
                                ->required()
                                ->options([
                                    '0' => 'Je n\'ai envoyé aucune relance',
                                    '1' => '1 relance envoyée',
                                    '2' => '2 relances envoyées',
                                    '3' => '3 relances envoyées',
                                ])
                        ])->columnSpan(1),
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Placeholder::make('last_relaunch')
                            ->label('Dernière relance')
                            ->content(fn (?UnpaidRecovery $record): string => $record->last_relaunch ? $record->last_relaunch->diffForHumans() : '-'),
                        Forms\Components\Placeholder::make('next_relaunch')
                            ->label('Prochaine relance')
                            ->content(fn (?UnpaidRecovery $record): string => $record->next_relaunch ? $record->next_relaunch->diffForHumans() : '-'),
                    ])
                    ->columnSpan(1),
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Créé')
                            ->content(fn (?UnpaidRecovery $record): string => $record ? $record->created_at->diffForHumans() : '-'),
                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Dernière modification')
                            ->content(fn (?UnpaidRecovery $record): string => $record ? $record->updated_at->diffForHumans() : '-'),
                    ])
                    ->columnSpan(1),
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
                Tables\Columns\TextColumn::make('ref')
                    ->label('Référence')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customerid')
                    ->label('Client')
                    ->getStateUsing(fn ($record): ?string => Customer::find($record->customerid)?->name ?? null)
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé')
                    ->date('d F Y H:i')
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Mise à jour')
                    ->date('d F Y H:i')
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->enum([
                        'ended' => 'Terminée',
                        'in_progress' => 'En cours',
                        'pending' => 'En attente',
                    ])
                    ->colors([
                        'danger' => 'pending',
                        'primary' => 'in_progress',
                        'success' => 'ended',
                    ])
                    ->searchable()
                    ->toggleable()
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
            'index' => Pages\ListUnpaidRecoveries::route('/'),
            'create' => Pages\CreateUnpaidRecovery::route('/create'),
            'edit' => Pages\EditUnpaidRecovery::route('/{record}/edit'),
        ];
    }    
}
