<?php

namespace App\Filament\App\Resources\SoalResource\Pages;

use App\Filament\App\Resources\SoalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSoal extends EditRecord
{
    protected static string $resource = SoalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
        ];
    }
}
