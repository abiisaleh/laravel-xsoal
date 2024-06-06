<?php

namespace App\Filament\App\Resources\SoalResource\Pages;

use App\Filament\App\Resources\SoalResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSoal extends CreateRecord
{
    protected static string $resource = SoalResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
}
