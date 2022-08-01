<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\ModelHasRole;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        ModelHasRole::create([
            'role_id' => $this->record->role,
            'model_type' => 'App\Models\User',
            'model_id' => $this->record->id,
        ]);
    }
}
