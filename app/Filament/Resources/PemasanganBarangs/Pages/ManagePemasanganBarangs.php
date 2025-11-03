<?php

namespace App\Filament\Resources\PemasanganBarangs\Pages;

use App\Filament\Resources\PemasanganBarangs\PemasanganBarangResource;
use App\Models\Pengadaan;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Colors\Color;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

class ManagePemasanganBarangs extends ManageRecords
{
    protected static string $resource = PemasanganBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Ekspor Pdf')
                ->color(Color::Amber)
                ->icon(Heroicon::OutlinedDocumentArrowDown)
                ->action(function () {
                    $records = $this->getTableRecords();

                    $tanggalPasang = $this->getTableFilterState('tanggal_pasang') ?? [];
                    $tglAwal = $tanggalPasang['tgl_awal'] ?? null;
                    $tglAkhir = $tanggalPasang['tgl_akhir'] ?? null;

                    $kondisiState = $this->getTableFilterState('baru_bekas');
                    $kondisiValue = is_array($kondisiState) ? ($kondisiState['value'] ?? null) : $kondisiState;

                    $kondisi = $kondisiValue
                        ? 'Baru/Bekas? : ' . ucfirst($kondisiValue)
                        : 'Baru/Bekas? : Semua';

                    if ($tglAwal && $tglAkhir) {
                        $periode = 'Periode: ' . Carbon::parse($tglAwal)->translatedFormat('d M Y') . ' - ' . Carbon::parse($tglAkhir)->translatedFormat('d M Y');
                    } elseif ($tglAwal) {
                        $periode = 'Periode: Mulai ' . Carbon::parse($tglAwal)->translatedFormat('d M Y');
                    } elseif ($tglAkhir) {
                        $periode = 'Periode: Sampai ' . Carbon::parse($tglAkhir)->translatedFormat('d M Y');
                    } else {
                        $periode = 'Periode: Semua';
                    }

                    $pdf = Pdf::loadView('exports.pemasangan', [
                        'records' => $records,
                        'kondisi' => $kondisi,
                        'periode' => $periode,
                        'exportDate' => now()->locale('id')->translatedFormat('j F Y H:i:s'),
                    ])->setPaper('a4', 'landscape');

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'laporan-pemasangan-' . now()->format('Y-m-d-His') . '.pdf'
                    );
                }),

            CreateAction::make()
                ->label('Pasang')
                ->modalWidth('md')
                ->icon(Heroicon::OutlinedWrenchScrewdriver)
                ->after(function ($data, $record) {
                    if ($data['baru_bekas'] === 'Baru' && !empty($data['barang_id'])) {
                        DB::transaction(function () use ($data) {
                            $pengadaan = Pengadaan::where('barang_id', $data['barang_id'])->first();
                            
                            if ($pengadaan && $pengadaan->jumlah > 0) {
                                $pengadaan->decrement('jumlah');
                            }
                        });
                    }
                })
        ];
    }
}
