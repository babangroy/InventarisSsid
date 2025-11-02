<?php

namespace App\Filament\Clusters\Master\Resources\Jenis\Pages;

use App\Filament\Clusters\Master\Resources\Jenis\JenisResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageJenis extends ManageRecords
{
    protected static string $resource = JenisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Jenis')
                ->modalWidth('md'),
        ];
    }
}
