<?php

namespace App\Filament\App\Resources\CategoryResource\Pages;

use App\Filament\App\Resources\CategoryResource;
use App\Models\Category;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCategories extends ManageRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data) {
                    $data['team_id'] = filament()->getTenant()->id;
                    return $data;
                })
        ];
    }
}
