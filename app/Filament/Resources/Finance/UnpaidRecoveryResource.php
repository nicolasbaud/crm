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
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction, AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;

class UnpaidRecoveryResource extends Resource
{
    protected static ?string $title = 'Recouvrement';
 
    protected static ?string $navigationLabel = 'Recouvrement';

    protected static ?string $modelLabel = 'Recouvrement';

    protected static ?string $model = UnpaidRecovery::class;

    protected static ?string $navigationGroup = 'Finance';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static function getNavigationBadge(): ?string
    {
        if (static::getModel()::where('status', 'in_progress')->Orwhere('status', 'pending')->count() != 0) {
            return static::getModel()::where('status', 'in_progress')->Orwhere('status', 'pending')->count();
        }
        return false;
    }

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
                                ->default('in_progress')
                                ->disabled(function (?UnpaidRecovery $record) {
                                    if ($record) {
                                        if ($record->status == 'ended')  {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    } else {
                                        return false;
                                    }
                                })
                                ->options([
                                    'ended' => 'Terminée',
                                    'in_progress' => 'En cours',
                                    'pending' => 'En attente'
                                ])
                                ->descriptions([
                                    'ended' => 'Le recouvrement est terminée.',
                                    'in_progress' => 'Le recouvrement débutera automatiquement.',
                                    'pending' => 'Le recouvrement ne commencera pas.',
                                ])
                        ])->columnSpan(1),
                    Forms\Components\Card::make()
                        ->schema([
                            Forms\Components\Placeholder::make('process')
                                ->label('Actions réalisées')
                                ->content(function (?UnpaidRecovery $record) {
                                    if ($record) {
                                        if (is_null($record->process)) {
                                            return '-';
                                        } else {
                                            return $record->process.' relance(s) envoyée(s)';
                                        }
                                    } else {
                                        return '-';
                                    }
                                }),
                        ])->columnSpan(1),
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Placeholder::make('last_relaunch')
                            ->label('Dernière relance')
                            ->content(function (?UnpaidRecovery $record) {
                                if ($record) {
                                    if (is_null($record->last_relaunch)) {
                                        return '-';
                                    } else {
                                        return $record->last_relaunch->diffForHumans();
                                    }
                                } else {
                                    return '-';
                                }
                            }),
                        Forms\Components\Placeholder::make('next_relaunch')
                            ->label('Prochaine relance')
                            ->content(function (?UnpaidRecovery $record) {
                                if ($record) {
                                    if (is_null($record->next_relaunch)) {
                                        return '-';
                                    } else {
                                        return $record->next_relaunch->diffForHumans();
                                    }
                                } else {
                                    return '-';
                                }
                            }),
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
                Tables\Columns\TextColumn::make('amount')
                    ->label('Montant')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Notes')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('process')
                    ->label('Processus')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('factured_at')
                    ->label('Date de facturation')
                    ->date('d F Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('echance_at')
                    ->label('Date d\'échéance')
                    ->date('d F Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_relaunch')
                    ->label('Dernière relance')
                    ->date('d F Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('next_relaunch')
                    ->label('Prochaine relance')
                    ->date('d F Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé')
                    ->date('d F Y H:i')
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Mise à jour')
                    ->date('d F Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true)
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
            'index' => Pages\ListUnpaidRecoveries::route('/'),
            'create' => Pages\CreateUnpaidRecovery::route('/create'),
            'edit' => Pages\EditUnpaidRecovery::route('/{record}/edit'),
        ];
    }    
}
