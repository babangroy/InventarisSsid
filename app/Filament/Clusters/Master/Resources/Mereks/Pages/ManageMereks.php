<?php

namespace App\Filament\Clusters\Master\Resources\Mereks\Pages;

use App\Filament\Clusters\Master\Resources\Mereks\MerekResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageMereks extends ManageRecords
{
    protected static string $resource = MerekResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Merek')
                ->modalWidth('md'),
        ];
    }
}
