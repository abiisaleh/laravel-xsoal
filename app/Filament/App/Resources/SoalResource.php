<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\SoalResource\Pages;
use App\Filament\Resources\SoalResource\RelationManagers;
use App\Models\Soal;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class SoalResource extends Resource
{
    protected static ?string $model = Soal::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Arsip';

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make()
                    ->columns()
                    ->schema([
                        Infolists\Components\TextEntry::make('judul'),
                        Infolists\Components\TextEntry::make('category.nama')
                            ->badge(),
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Penulis'),
                        Infolists\Components\TextEntry::make('jumlah_soal'),
                    ]),

                Infolists\Components\Section::make('Detail soal')
                    ->columns(3)
                    ->schema([
                        Infolists\Components\TextEntry::make('detail.studi')->label('Studi'),
                        Infolists\Components\TextEntry::make('detail.kelas')->label('Kelas'),
                        Infolists\Components\TextEntry::make('detail.pengajar')->label('Pengajar'),

                        Infolists\Components\TextEntry::make('tanggal_soal')->label('Hari / Tanggal')->icon('heroicon-o-calendar-days'),
                        Infolists\Components\TextEntry::make('detail.waktu_mulai')->label('Waktu mulai')->icon('heroicon-o-clock'),
                        Infolists\Components\TextEntry::make('detail.waktu_selesai')->label('Waktu selesai')->icon('heroicon-o-clock'),

                    ]),

                Infolists\Components\RepeatableEntry::make('pilihan_ganda')
                    ->schema([
                        Infolists\Components\TextEntry::make('pertanyaan')
                            ->prefix('1. ')
                            ->hiddenLabel()
                            ->prefix(function (Soal $record, $state) {
                                $haystack = array_map(fn ($item) => $item['pertanyaan'], $record->pilihan_ganda);

                                $key = array_search($state, $haystack);

                                $keys = array_keys($haystack);

                                $position = array_search($key, $keys) + 1;

                                return $position . '. ';
                            }),
                        Infolists\Components\ImageEntry::make('gambar')
                            ->hidden(fn ($state) => is_null($state)),
                        Infolists\Components\TextEntry::make('opsi'),
                    ]),
                Infolists\Components\RepeatableEntry::make('essay')
                    ->schema([
                        Infolists\Components\TextEntry::make('pertanyaan')
                            ->hiddenLabel()
                            ->prefix(function (Soal $record, $state) {
                                $haystack = array_map(fn ($item) => $item['pertanyaan'], $record->essay);

                                $key = array_search($state, $haystack);

                                $keys = array_keys($haystack);

                                $position = array_search($key, $keys) + 1;

                                return $position . '. ';
                            }),
                        Infolists\Components\ImageEntry::make('gambar')
                            ->hidden(fn ($state) => is_null($state))
                            ->hiddenLabel(),
                    ]),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('judul')
                    ->required()
                    ->placeholder('Ujian Akhir Matematika Kelas V Semseter I')
                    ->maxLength(255),
                Forms\Components\Select::make('category_id')
                    ->required()
                    ->native(false)
                    ->relationship('category', 'nama'),

                Forms\Components\Section::make('Detail soal')
                    ->collapsed(false)
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('detail.studi')->placeholder('Matematika'),
                        Forms\Components\TextInput::make('detail.kelas')->placeholder('V'),
                        Forms\Components\TextInput::make('detail.pengajar')->placeholder('John Doe'),
                        Forms\Components\DatePicker::make('detail.tanggal')->minDate(now()->addDay()),
                        Forms\Components\TimePicker::make('detail.waktu_mulai')->seconds(false),
                        Forms\Components\TimePicker::make('detail.waktu_selesai')->seconds(false),
                    ]),

                Forms\Components\Repeater::make('pilihan_ganda')
                    ->required()
                    ->collapsible()
                    ->collapsed()
                    ->itemLabel(function (Get $get, array $state) {
                        $key = array_search($state, $get('pilihan_ganda'));

                        $keys = array_keys($get('pilihan_ganda'));
                        $position = array_search($key, $keys) + 1;

                        return $position . '. ' . $state['pertanyaan'];
                    })
                    ->schema([
                        Forms\Components\Textarea::make('pertanyaan'),
                        Forms\Components\FileUpload::make('gambar')
                            ->image()
                            ->previewable(),
                        Forms\Components\Repeater::make('opsi')
                            ->simple(
                                Forms\Components\TextInput::make('opsi')
                            )
                            ->defaultItems(4)
                    ]),

                Forms\Components\Repeater::make('essay')
                    ->collapsible()
                    ->collapsed()
                    ->itemLabel(function (Get $get, array $state) {
                        $key = array_search($state, $get('essay'));

                        $keys = array_keys($get('essay'));
                        $position = array_search($key, $keys) + 1;

                        return $position . '. ' . $state['pertanyaan'];
                    })
                    ->schema([
                        Forms\Components\Textarea::make('pertanyaan'),
                        Forms\Components\FileUpload::make('gambar')
                            ->image()
                            ->previewable(),
                    ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('Penulis'),
                Tables\Columns\TextColumn::make('category.nama')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah_soal'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'nama')
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('gray'),
                    Tables\Actions\EditAction::make()
                        ->color('gray'),
                    Tables\Actions\Action::make('Print')
                        ->icon('heroicon-s-printer')
                        ->color('gray')
                        ->action(function (Soal $soal) {
                            $jumlahPG = collect($soal->pilihan_ganda)->count();
                            $jumlahEssay = collect($soal->essay)->count();

                            //generate dari template
                            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('doc/Template-full.docx');

                            $param = [
                                'sekolah' => $soal->team->name ?? '',
                                'alamat' => $soal->team->alamat ?? 'alamat',
                                'kontak' => $soal->team->kontak ?? '',

                                'judul' => $soal->judul,
                                'studi' => $soal->detail['studi'] ?? '',
                                'tanggal' => $soal->tanggal_soal  ?? '',
                                'waktu' => ($soal->detail['waktu_mulai'] . ' sampai ' . $soal->detail['waktu_selesai']) ?? '',
                                'kelas' => $soal->detail['kelas'] ?? '',
                                'pengajar' => $soal->detail['pengajar'] ?? '',
                            ];

                            $templateProcessor->setValues($param);

                            if (!is_null($soal->team->logo_kanan))
                                $templateProcessor->setImageValue('logo_kanan', 'storage/' . $soal->team->logo_kanan);
                            else
                                $templateProcessor->setValue('logo_kanan', '');

                            if (!is_null($soal->team->logo_kiri))
                                $templateProcessor->setImageValue('logo_kiri', 'storage/' . $soal->team->logo_kiri);
                            else
                                $templateProcessor->setValue('logo_kiri', '');


                            //soal PG
                            $templateProcessor->cloneBlock('soalPG', $jumlahPG, true, true);

                            foreach (collect($soal->pilihan_ganda) as $key => $pilihan_ganda) {
                                $no = $key + 1;
                                $opsi = [];

                                $templateProcessor->setValue('pertanyaanPG#' . $no, $pilihan_ganda['pertanyaan']);

                                if (!is_null($pilihan_ganda['gambar']))
                                    $templateProcessor->setImageValue('gambar#' . $no, 'storage/' . $pilihan_ganda['gambar']);
                                else
                                    $templateProcessor->setValue('gambar#' . $no, '');

                                foreach ($pilihan_ganda['opsi'] as $jawaban)
                                    $opsi[]['jawabanPG#' . $no] = $jawaban;

                                $templateProcessor->cloneBlock('opsi#' . $no, 0, true, false, $opsi);
                            }

                            //soal Essay
                            $templateProcessor->cloneBlock('soalEssay', $jumlahEssay, true, true);

                            foreach (collect($soal->essay) as $key => $essay) {
                                $no = $key + 1;

                                $templateProcessor->setValue('pertanyaanEssay#' . $no, $essay['pertanyaan']);

                                if (!is_null($essay['gambar']))
                                    $templateProcessor->setImageValue('gambarEssay#' . $no, 'storage/' . $essay['gambar']);
                                else
                                    $templateProcessor->setValue('gambarEssay#' . $no, '');
                            }

                            // Simpan dokumen yang sudah diproses ke file sementara
                            $tempFile = tempnam(sys_get_temp_dir(), 'phpword');
                            $templateProcessor->saveAs($tempFile);

                            // Atur header untuk download
                            $fileName = \Illuminate\Support\Str::slug($soal->judul) . '-' . now()->timestamp . '.docx';
                            $headers = [
                                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'Content-Disposition' => 'attachment;filename="' . $fileName . '"',
                                'Cache-Control' => 'max-age=0',
                                'Pragma' => 'public'
                            ];

                            // Kirim file sebagai respon dan hapus file sementara setelah dikirim
                            return response()->download($tempFile, $fileName, $headers)->deleteFileAfterSend(true);
                        }),
                    Tables\Actions\DeleteAction::make()
                ])->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListSoals::route('/'),
            'create' => Pages\CreateSoal::route('/create'),
            'edit' => Pages\EditSoal::route('/{record}/edit'),
            'view' => Pages\ViewSoal::route('/{record}/view'),
        ];
    }

    public static function canEdit(Model $record): bool
    {
        return $record->user_id == auth()->id();
    }

    public static function canDelete(Model $record): bool
    {
        return $record->user_id == auth()->id();
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->is_team_admin;
    }
}
