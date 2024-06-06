<?php

namespace App\Filament\App\Resources\SoalResource\Pages;

use App\Filament\App\Resources\SoalResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListSoals extends ListRecords
{
    protected static string $resource = SoalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'soalku' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', auth()->id())),
            'semua' => Tab::make(),
        ];
    }
}
