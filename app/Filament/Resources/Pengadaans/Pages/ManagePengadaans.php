<?php

namespace App\Filament\Resources\Pengadaans\Pages;

use App\Filament\Resources\Pengadaans\PengadaanResource;
use App\Models\Barang;
use App\Models\Pengadaan;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Schema;

class ManagePengadaans extends ManageRecords
{
    protected static string $resource = PengadaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('tambah')
                ->label('Buat Pengadaan')
                ->modalWidth('md')
                ->schema(PengadaanResource::getSchema())
                ->action(function (array $data) {
                    $barang = Barang::find($data['barang_id']);
                    
                    $pengadaan = Pengadaan::create([
                        'barang_id' => $data['barang_id'],
                        'jenis_id' => $barang->jenis_id,
                        'merek_id' => $barang->merek_id,
                        'jumlah' => $data['jumlah'],
                        'tgl_masuk' => $data['tgl_masuk'],
                    ]);

                    Notification::make()
                        ->title('Pengadaan berhasil ditambahkan')
                        ->success()
                        ->send();

                    $this->refresh();
                }),
        ];
    }
}
