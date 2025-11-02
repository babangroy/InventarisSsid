<?php

namespace App\Filament\Clusters\Master\Resources\Barangs\Pages;

use App\Filament\Clusters\Master\Resources\Barangs\BarangResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageBarangs extends ManageRecords
{
    protected static string $resource = BarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Barang')
                ->modalWidth('md'),
        ];
    }
}
