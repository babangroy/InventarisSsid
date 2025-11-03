<?php

namespace App\Filament\Resources\Pengadaans;

use App\Filament\Resources\Pengadaans\Pages\ManagePengadaans;
use App\Models\Barang;
use App\Models\Jenis;
use App\Models\Merek;
use App\Models\Pengadaan;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PengadaanResource extends Resource
{
    protected static ?string $model = Pengadaan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'barang_id';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('barang_id')
                    ->label('Nama Barang')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->options(
                        Barang::with(['jenis', 'merek'])
                            ->orderBy('nama')
                            ->get()
                            ->mapWithKeys(fn($barang) => [
                                $barang->id => "{$barang->nama} - {$barang->merek?->nama} ({$barang->jenis?->nama})"
                            ])
                    )
                    ->createOptionForm([
                        TextInput::make('nama')
                            ->label('Nama Barang')
                            ->required()
                            ->maxLength(255)
                            ->validationMessages([
                                'required' => 'Nama Barang wajib diisi.',
                                'max.length' => 'Nama Barang maksimal :max karakter.',
                            ]),
                        
                        Select::make('jenis_id')
                            ->label('Jenis')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->options(
                                Jenis::orderBy('nama')->pluck('nama', 'id')
                            )
                            ->createOptionForm([
                                TextInput::make('nama')
                                    ->label('Nama Jenis')
                                    ->required()
                                    ->maxLength(40)
                                    ->validationMessages([
                                        'required' => 'Nama Jenis wajib diisi.',
                                        'max.length' => 'Nama Jenis maksimal :max karakter.',
                                    ])
                            ])
                            ->createOptionAction(
                                fn (Action $action) => $action->modalWidth('md')->modalHeading('Tambah Jenis')
                            )
                            ->createOptionUsing(function (array $data) {
                                $jenis = Jenis::create($data);
                                return $jenis->id;
                            })
                            ->validationMessages([
                                'required' => 'Jenis wajib dipilih.',
                            ]),
                        
                        Select::make('merek_id')
                            ->label('Merek')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->options(
                                Merek::orderBy('nama')->pluck('nama', 'id')
                            )
                            ->createOptionForm([
                                TextInput::make('nama')
                                    ->label('Nama Merek')
                                    ->required()
                                    ->maxLength(40)
                                    ->validationMessages([
                                        'required' => 'Nama Merek wajib diisi.',
                                        'max.length' => 'Nama Merek maksimal :max karakter.',
                                    ])
                            ])
                            ->createOptionAction(
                                fn (Action $action) => $action->modalWidth('md')->modalHeading('Tambah Merek')
                            )
                            ->createOptionUsing(function (array $data) {
                                $merek = Merek::create($data);
                                return $merek->id;
                            })
                            ->validationMessages([
                                'required' => 'Merek wajib dipilih.',
                            ]),
                    ])
                    ->createOptionAction(
                        fn (Action $action) => $action->modalWidth('md')->modalHeading('Tambah Barang Baru')
                    )
                    ->createOptionUsing(function (array $data) {
                        $barang = Barang::create([
                            'nama' => $data['nama'],
                            'jenis_id' => $data['jenis_id'],
                            'merek_id' => $data['merek_id'],
                        ]);
                        return $barang->id;
                    })
                    ->validationMessages([
                        'required' => 'Nama Barang tidak boleh kosong',
                    ])
                    ->columnSpanFull(),

                TextInput::make('jumlah')
                    ->required()
                    ->numeric()
                    ->columnSpanFull(),
                DatePicker::make('tgl_masuk')
                    ->required()
                    ->native(false)
                    ->displayFormat('d M Y')
                    ->columnSpanFull(),
            ]);
    }

    public static function getSchema(): array
    {
        static $cached;

        if (! $cached) {
            $schema = new Schema;
            $cached = static::form($schema)->getComponents();
        }

        return $cached;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('barang_id')
            ->columns([
                TextColumn::make('no')
                    ->label('No.')
                    ->rowIndex(),

                TextColumn::make('barang.nama')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('merek.nama')
                    ->label('Merek')
                    ->sortable(),

                TextColumn::make('jenis.nama')
                    ->label('Jenis')
                    ->sortable(),

                TextColumn::make('jumlah')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('tgl_masuk')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Ubah')
                    ->modalWidth('md')
                    ->after(function ($record, array $data) {
                        $barang = Barang::find($data['barang_id']);

                        if ($barang) {
                            $record->update([
                                'jenis_id' => $barang->jenis_id,
                                'merek_id' => $barang->merek_id,
                            ]);
                        }
                    }),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePengadaans::route('/'),
        ];
    }
}
