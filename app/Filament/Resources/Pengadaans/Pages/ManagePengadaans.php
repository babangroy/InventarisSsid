<?php

namespace App\Filament\Resources\Pengadaans\Pages;

use App\Filament\Resources\Pengadaans\PengadaanResource;
use App\Models\Barang;
use App\Models\Pengadaan;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManagePengadaans extends ManageRecords
{
    protected static string $resource = PengadaanResource::class;

        protected function getHeaderActions(): array
        {
            return [
                CreateAction::make('tambah')
                    ->label('Buat Pengadaan')
                    ->modalWidth('md')
                    ->using(function (array $data, string $model): Pengadaan {
                        $barang = Barang::find($data['barang_id']);

                        return Pengadaan::create([
                            'barang_id'   => $data['barang_id'],
                            'jenis_id'    => $barang?->jenis_id,
                            'merek_id'    => $barang?->merek_id,
                            'jumlah_awal' => $data['jumlah'],
                            'jumlah'      => $data['jumlah'],
                            'tgl_masuk'   => $data['tgl_masuk'],
                        ]);
                    })
                    ->successNotification(
                        Notification::make()
                            ->title('Data pengadaan berhasil dibuat')
                            ->success()
                    ),
            ];
        }
}