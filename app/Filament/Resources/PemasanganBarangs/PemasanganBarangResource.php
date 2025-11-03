<?php

namespace App\Filament\Resources\PemasanganBarangs;

use App\Filament\Resources\PemasanganBarangs\Pages\ManagePemasanganBarangs;
use App\Models\Barang;
use App\Models\BarangDitarik;
use App\Models\BarangKeluar;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class PemasanganBarangResource extends Resource
{
    protected static ?int $navigationSort = 1;

    protected static ?string $model = BarangKeluar::class;

    protected static ?string $modelLabel = 'Pemasangan Barang';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowLeftStartOnRectangle;

    protected static string | UnitEnum | null $navigationGroup = 'Kegiatan';

    protected static ?string $recordTitleAttribute = 'sn';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('barang_id')
                    ->label('Nama Barang')
                    ->required()
                    ->preload()
                    ->native(false)
                    ->searchable()
                    ->options(
                        Barang::with(['jenis:id,nama', 'merek:id,nama'])
                            ->orderBy('nama')
                            ->get()
                            ->mapWithKeys(function ($barang) {
                                $jenis = $barang->jenis?->nama ?? '-';
                                $merek = $barang->merek?->nama ?? '-';
                                return [
                                    $barang->id => "{$barang->nama} - {$merek} ({$jenis})"
                                ];
                            })
                    )
                    ->validationMessages([
                        'required' => 'Nama Barang tidak boleh kosong',
                    ])
                    ->columnSpanFull(),

                TextInput::make('sn')
                    ->label('Serial Number')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(50)
                    ->validationMessages([
                        'required' => 'Serial Number tidak boleh kosong',
                        'maxLength' => 'Serial Number maksimal :max karakter',
                        'unique' => 'Serial Number sudah terdaftar',
                    ])
                    ->columnSpanFull(),

                TextInput::make('tujuan')
                    ->label('Tujuan Pemasangan')
                    ->required()
                    ->maxLength(50)
                    ->validationMessages([
                        'required' => 'Tujuan Pemasangan tidak boleh kosong',
                        'maxLength' => 'Tujuan Pemasangan maksimal :max karakter',
                    ])
                    ->columnSpanFull(),

                DatePicker::make('tanggal_pasang')
                    ->label('Tanggal Pemasangan')
                    ->native(false)
                    ->displayFormat('d M Y')
                    ->required()
                    ->validationMessages([
                        'required' => 'Tanggal Pemasangan tidak boleh kosong',
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('sn')
            ->columns([
                TextColumn::make('no')
                    ->label('No.')
                    ->rowIndex()
                    ->width('70px'),

                TextColumn::make('barang.nama')
                    ->label('Nama/Tipe')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('barang.merek.nama')
                    ->label('Merek')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('barang.jenis.nama')
                    ->label('Jenis')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('sn')
                    ->label('Serial Number')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('tujuan')
                    ->label('Tempat Pemasangan')
                    ->sortable(),

                TextColumn::make('tanggal_pasang')
                    ->label('Tanggal Pemasangan')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('tanggal_pasang')
                    ->schema([
                        DatePicker::make('tgl_awal')
                            ->label('Tanggal Awal')
                            ->native(false)
                            ->displayFormat('d M Y'),
                        DatePicker::make('tgl_akhir')
                            ->label('Tanggal Akhir')
                            ->native(false)
                            ->displayFormat('d M Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['tgl_awal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_pasang', '>=', $date),
                            )
                            ->when(
                                $data['tgl_akhir'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_pasang', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['tgl_awal'] ?? null) {
                            $indicators[] = Indicator::make('Dipasang mulai ' . Carbon::parse($data['tgl_awal'])->locale('id')->translatedFormat('d M Y'))
                                ->removeField('tgl_awal');

                        }

                        if ($data['tgl_akhir'] ?? null) {
                            $indicators[] = Indicator::make('Dipasang sampai ' . Carbon::parse($data['tgl_akhir'])->locale('id')->translatedFormat('d M Y'))
                                ->removeField('tgl_akhir');
                        }

                        return $indicators;
                    })
            ])
            ->recordActions([
                Action::make('tarik')
                    ->label('Tarik')
                    ->color(Color::Amber)
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->modalWidth('md')
                    ->modalSubmitActionLabel('Tarik')
                    ->modalHeading(fn($record) => 'Tarik ' . ($record?->barang?->nama ?? 'Barang'))
                    ->schema([
                        Textarea::make('alasan')
                            ->label('Alasan')
                            ->required()
                            ->maxLength(255)
                            ->validationMessages([
                                'required' => 'Alasan penarikan wajib diisi.',
                                'max_length' => 'Alasan penarikan maksimal :max karakter.',
                            ]),

                        Select::make('kondisi')
                            ->label('Kondisi')
                            ->options([
                                'Baik' => 'Baik',
                                'Rusak' => 'Rusak',
                                'Hilang' => 'Hilang',
                            ])
                            ->native(false)
                            ->required()
                            ->validationMessages([
                                'required' => 'Kondisi barang wajib dipilih.',
                            ]),

                        DatePicker::make('tanggal_tarik')
                            ->label('Tanggal Tarik')
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->required()
                            ->validationMessages([
                                'required' => 'Tanggal tarik wajib diisi.',
                            ]),
                    ])
                    ->action(function ($record, array $data) {
                        if (! $record) {
                            throw new \Exception('Record tidak ditemukan.');
                        }

                        BarangDitarik::create([
                            'barang_id' => $record->barang_id,
                            'sn' => $record->sn,
                            'asal' => $record->tujuan,
                            'alasan' => $data['alasan'],
                            'kondisi' => $data['kondisi'],
                            'tanggal_tarik' => $data['tanggal_tarik'],
                        ]);

                        $record->delete();

                        Notification::make()
                            ->title('Berhasil')
                            ->body('Barang berhasil ditarik dan dihapus dari daftar pemasangan.')
                            ->success()
                            ->send();
                    }),

                EditAction::make()
                    ->label('Ubah')
                    ->modalWidth('md'),
                DeleteAction::make()
                    ->label('Hapus'),
                ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePemasanganBarangs::route('/'),
        ];
    }
}
