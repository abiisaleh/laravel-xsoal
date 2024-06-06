<?php

namespace App\Filament\App\Resources\SoalResource\Pages;

use App\Filament\App\Resources\SoalResource;
use App\Models\Soal;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSoal extends ViewRecord
{
    protected static string $resource = SoalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
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
            Actions\EditAction::make()
        ];
    }
}
