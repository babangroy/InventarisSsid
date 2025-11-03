<?php

namespace App\Filament\Widgets;

use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Pengadaan;
use Filament\Tables\Columns\TextColumn;

class PengadaanPreview extends TableWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 2;

    protected static ?string $heading = '10 Pengadaan Barang Terbaru';

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Pengadaan::query()->orderBy('tgl_masuk', 'desc')->limit(10))
            ->columns([
                TextColumn::make('no')
                    ->label('No.')
                    ->rowIndex(),

                TextColumn::make('barang.nama')
                    ->label('Nama Barang'),

                TextColumn::make('merek.nama')
                    ->label('Merek'),

                TextColumn::make('jenis.nama')
                    ->label('Jenis'),

                TextColumn::make('jumlah'),

                TextColumn::make('tgl_masuk')
                    ->date('d M Y'),
            ])
            ->paginated(false);
    }
}
