<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\BarangDitariks\Pages\ManageBarangDitariks;
use App\Filament\Resources\PemasanganBarangs\Pages\ManagePemasanganBarangs;
use App\Models\BarangDitarik;
use App\Models\BarangKeluar;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PemasaganWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $JumlahDigunakan = BarangKeluar::query()->count();
        $JumlahDitarikBagus = BarangDitarik::where('kondisi', 'Baik')->count();
        $JumlahDitarikRusak = BarangDitarik::where('kondisi', 'Rusak')->count();


        return [
            Stat::make('Barang terpasang', $JumlahDigunakan)
                ->description('Jumlah barang yang terpasang')
                ->descriptionIcon(Heroicon::OutlinedWrenchScrewdriver)    
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color(Color::Amber)
                ->url(ManagePemasanganBarangs::getUrl()),

            Stat::make('Barang ditarik', $JumlahDitarikBagus)
                ->description('Jumlah barang ditarik kondisi baik')
                ->descriptionIcon(Heroicon::OutlinedWrenchScrewdriver)    
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color(Color::Green)
                ->url(ManageBarangDitariks::getUrl()),

            Stat::make('Barang ditarik', $JumlahDitarikRusak)
                ->description('Jumlah barang ditarik kondisi rusak')
                ->descriptionIcon(Heroicon::OutlinedWrenchScrewdriver)    
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color(Color::Red)
                ->url(ManageBarangDitariks::getUrl()),
        ];
    }
}
