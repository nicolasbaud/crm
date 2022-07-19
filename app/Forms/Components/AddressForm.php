<?php

namespace App\Forms\Components;

use Filament\Forms;
use Illuminate\Database\Eloquent\Model;
use Squire\Models\Country;

class AddressForm extends Forms\Components\Field
{
    protected string $view = 'forms::components.group';

    public $relationship = null;

    public function relationship(string | callable $relationship): static
    {
        $this->relationship = $relationship;

        return $this;
    }

    public function saveRelationships(): void
    {
        $state = $this->getState();
        $record = $this->getRecord();
        $relationship = $record->{$this->getRelationship()}();

        if ($address = $relationship->first()) {
            $address->update($state);
        } else {
            $relationship->updateOrCreate($state);
        }

        $record->touch();
    }

    public function getChildComponents(): array
    {
        return [
            Forms\Components\TextInput::make('street')
                ->label('Adresse')
                        ->required(),
            Forms\Components\Grid::make(3)
                ->schema([
                    Forms\Components\TextInput::make('city')->label('Ville')
                        ->required(),
                    Forms\Components\TextInput::make('state')
                        ->label('Région')
                        ->required(),
                    Forms\Components\TextInput::make('zip')
                        ->label('Code Postal')
                        ->required(),
                ]),
            Forms\Components\Grid::make(1)
                ->schema([
                    Forms\Components\Select::make('country')
                        ->label('Pays')
                        ->searchable()
                        ->getSearchResultsUsing(fn (string $query) => Country::where('name', 'like', "%{$query}%")->pluck('name', 'id'))
                        ->getOptionLabelUsing(fn ($value): ?string => Country::find($value)?->name)
                        ->required(),
                ]),
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (AddressForm $component, ?Model $record) {
            $address = $record?->getRelationValue($this->getRelationship());

            $component->state($address ? $address->toArray() : [
                'country' => null,
                'street' => null,
                'city' => null,
                'state' => null,
                'zip' => null,
            ]);
        });

        $this->dehydrated(false);
    }

    public function getRelationship(): string
    {
        return $this->evaluate($this->relationship) ?? $this->getName();
    }
}