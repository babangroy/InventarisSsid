<?php

namespace App\Filament\Resources\Ssids\Pages;

use App\Filament\Resources\Ssids\SsidResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSsids extends ManageRecords
{
    protected static string $resource = SsidResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah SSID')
                ->modalWidth('md'),
        ];
    }
}
