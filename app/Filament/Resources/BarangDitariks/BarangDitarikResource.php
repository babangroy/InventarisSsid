<?php

namespace App\Filament\Resources\BarangDitariks;

use App\Filament\Resources\BarangDitariks\Pages\ManageBarangDitariks;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class BarangDitarikResource extends Resource
{
    protected static ?int $navigationSort = 2;

    protected static ?string $model = BarangDitarik::class;

    protected static ?string $modelLabel = 'Penarikan Barang';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowLeftOnRectangle;

    protected static string | UnitEnum | null $navigationGroup = 'Kegiatan';

    protected static ?string $recordTitleAttribute = 'sn';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sn')
                    ->label('Serial Number')
                    ->required()
                    ->maxLength(100)
                    ->validationMessages([
                        'required' => 'Serial Number wajib diisi.',
                        'max_length' => 'Serial Number maksimal :max karakter.',
                    ])
                    ->columnSpanFull(),

                Textarea::make('alasan')
                    ->label('Alasan Penarikan')
                    ->required()
                    ->maxLength(255)
                    ->validationMessages([
                        'required' => 'Alasan Penarikan wajib diisi.',
                        'max_length' => 'Alasan Penarikan maksimal :max karakter.',
                    ])
                    ->columnSpanFull(),

                Select::make('kondisi')
                    ->label('Kondisi')
                    ->required()
                    ->options([
                        'Baik' => 'Baik',
                        'Rusak' => 'Rusak',
                        'Hilang' => 'Hilang',
                    ])
                    ->validationMessages([
                        'required' => 'Kondisi wajib diisi.',
                    ])
                    ->native(false)
                    ->columnSpanFull(),

                DatePicker::make('tanggal_tarik')
                    ->label('Tanggal Ditarik')
                    ->required()
                    ->validationMessages([
                        'required' => 'Tanggal Ditarik wajib diisi.',
                    ])
                    ->native(false)
                    ->displayFormat('d M Y')
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
                    ->searchable()
                    ->sortable(),

                TextColumn::make('barang.merek.nama')
                    ->label('Merek')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('barang.jenis.nama')
                    ->label('Jenis')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sn')
                    ->label('Serial Number')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('asal')
                    ->label('Asal')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('alasan')
                    ->label('Alasan Penarikan'),

                TextColumn::make('kondisi')
                    ->label('Kondisi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Baik' => 'success',
                        'Rusak' => 'danger',
                        'Hilang' => 'gray',
                    }),

                TextColumn::make('tanggal_tarik')
                    ->label('Tanggal Ditarik')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('kondisi')
                    ->options([
                        'Baik' => 'Baik',
                        'Rusak' => 'Rusak',
                        'Hilang' => 'Hilang',
                    ])
                    ->native(false),

                Filter::make('tanggal_tarik')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_tarik', '>=', $date),
                            )
                            ->when(
                                $data['tgl_akhir'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_tarik', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['tgl_awal'] ?? null) {
                            $indicators[] = Indicator::make('Ditarik mulai ' . Carbon::parse($data['tgl_awal'])->locale('id')->translatedFormat('d M Y'))
                                ->removeField('tgl_awal');

                        }

                        if ($data['tgl_akhir'] ?? null) {
                            $indicators[] = Indicator::make('Ditarik sampai ' . Carbon::parse($data['tgl_akhir'])->locale('id')->translatedFormat('d M Y'))
                                ->removeField('tgl_akhir');
                        }

                        return $indicators;
                    })
            ])
            ->recordActions([
                Action::make('pasang')
                    ->visible(fn($record) => $record && $record->kondisi === 'Baik')
                    ->label('Pasang')
                    ->color(Color::Amber)
                    ->icon(Heroicon::WrenchScrewdriver)
                    ->modalWidth('md')
                    ->modalSubmitActionLabel('Pasang')
                    ->modalHeading(fn($record) => 'Pasang ' . ($record?->barang?->nama ?? 'Barang'))
                    ->schema([
                        TextInput::make('tujuan')
                            ->label('Tujuan')
                            ->required()
                            ->maxLength(255)
                            ->validationMessages([
                                'required' => 'Tujuan penarikan wajib diisi.',
                                'max_length' => 'Tujuan penarikan maksimal :max karakter.',
                            ]),

                        DatePicker::make('tanggal_pasang')
                            ->label('Tanggal Pasang')
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->required()
                            ->validationMessages([
                                'required' => 'Tanggal pasang wajib diisi.',
                            ]),
                    ])
                    ->action(function ($record, array $data) {
                        if (! $record) {
                            throw new \Exception('Record tidak ditemukan.');
                        }

                        BarangKeluar::create([
                            'baru_bekas' => 'Bekas',
                            'barang_id' => $record->barang_id,
                            'sn' => $record->sn,
                            'tujuan' => $data['tujuan'],
                            'tanggal_pasang' => $data['tanggal_pasang'],
                        ]);

                        $record->delete();

                        Notification::make()
                            ->title('Berhasil')
                            ->body('Barang berhasil digunakan kembali.')
                            ->success()
                            ->send();
                    }),
                    
                EditAction::make()
                    ->label('Ubah')
                    ->modalHeading(fn($record) => 'Ubah Barang ' . ($record?->barang?->nama ?? 'Barang'))
                    ->modalWidth('md'),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageBarangDitariks::route('/'),
        ];
    }
}
