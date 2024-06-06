<?php

namespace App\Filament\App\Resources\UserResource\Pages;

use App\Filament\App\Resources\UserResource;
use App\Models\Team;
use App\Models\User;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->after(function (User $user) {
                    return $user->teams()->updateExistingPivot(filament()->getTenant()->id, ['is_admin' => false]);
                })
        ];
    }
}
