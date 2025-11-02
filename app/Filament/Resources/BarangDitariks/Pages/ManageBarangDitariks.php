<?php

namespace App\Filament\Resources\BarangDitariks\Pages;

use App\Filament\Resources\BarangDitariks\BarangDitarikResource;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;

class ManageBarangDitariks extends ManageRecords
{
    protected static string $resource = BarangDitarikResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Ekspor Pdf')
                ->color(Color::Amber)
                ->icon(Heroicon::OutlinedDocumentArrowDown)
                ->action(function () {
                    $records = $this->getTableRecords();

                    $tanggaltarik = $this->getTableFilterState('tanggal_tarik') ?? [];
                    $tglAwal = $tanggaltarik['tgl_awal'] ?? null;
                    $tglAkhir = $tanggaltarik['tgl_akhir'] ?? null;

                    $kondisiState = $this->getTableFilterState('kondisi');
                    $kondisiValue = is_array($kondisiState) ? ($kondisiState['value'] ?? null) : $kondisiState;

                    $kondisi = $kondisiValue
                        ? 'Kondisi Barang: ' . ucfirst($kondisiValue)
                        : 'Kondisi Barang: Semua';

                    if ($tglAwal && $tglAkhir) {
                        $periode = 'Periode: ' . Carbon::parse($tglAwal)->translatedFormat('d M Y') .
                            ' - ' . Carbon::parse($tglAkhir)->translatedFormat('d M Y');
                    } elseif ($tglAwal) {
                        $periode = 'Periode: Mulai ' . Carbon::parse($tglAwal)->translatedFormat('d M Y');
                    } elseif ($tglAkhir) {
                        $periode = 'Periode: Sampai ' . Carbon::parse($tglAkhir)->translatedFormat('d M Y');
                    } else {
                        $periode = 'Periode: Semua';
                    }

                    $pdf = Pdf::loadView('exports.penarikan', [
                        'records' => $records,
                        'kondisi' => $kondisi,
                        'periode' => $periode,
                        'exportDate' => now()->locale('id')->translatedFormat('j F Y H:i:s'),
                    ])->setPaper('a4', 'landscape');

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'laporan-penarikan-' . now()->format('Y-m-d-His') . '.pdf'
                    );
                }),
        ];
    }
}