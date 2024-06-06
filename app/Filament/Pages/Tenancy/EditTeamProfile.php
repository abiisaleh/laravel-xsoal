<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;

class EditTeamProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Team profile';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                Textarea::make('alamat'),
                Fieldset::make('Kontak')->schema([
                    TextInput::make('telp')->tel(),
                    TextInput::make('fax'),
                    TextInput::make('email')->email(),
                ])->columns(3),
                Section::make('Logo')->schema([
                    FileUpload::make('logo_kiri')->image(),
                    FileUpload::make('logo_kanan')->image(),
                ])->columns()
                    ->description('digunakan untuk header pada saat cetak soal')
            ]);
    }
}
