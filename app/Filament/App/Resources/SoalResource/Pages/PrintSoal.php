<?php

namespace App\Filament\App\Resources\SoalResource\Pages;

use App\Filament\App\Resources\SoalResource;
use App\Models\Template;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;

class PrintSoal extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = SoalResource::class;

    protected static ?string $breadcrumb = 'Print';

    protected static string $view = 'filament.app.resources.soal-resource.pages.print-soal';

    public function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Grid::make(1)
                    ->columnSpan(1)
                    ->schema([
                        Select::make('template')
                            ->options(fn () => Template::all()->pluck('name', 'file')),
                        Select::make('kolom')
                            ->default(1)
                            ->options([
                                1 => 1,
                                2 => 2,
                            ]),
                        Fieldset::make('margin')
                            ->schema([
                                TextInput::make('top')->numeric()->suffix('cm'),
                                TextInput::make('bottom')->numeric()->suffix('cm'),
                                TextInput::make('left')->numeric()->suffix('cm'),
                                TextInput::make('right')->numeric()->suffix('cm'),
                            ])
                    ]),

                Grid::make(1)
                    ->columnSpan(1)
                    ->schema([
                        ViewField::make('preview')
                            ->view('filament.forms.components.print-preview')
                            ->viewData([
                                'soal' => 'haha'
                            ])
                    ]),
            ]);
    }
}
