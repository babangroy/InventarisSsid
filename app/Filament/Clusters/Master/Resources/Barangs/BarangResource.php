<?php

namespace App\Filament\Clusters\Master\Resources\Barangs;

use App\Filament\Clusters\Master\MasterCluster;
use App\Filament\Clusters\Master\Resources\Barangs\Pages\ManageBarangs;
use App\Models\Barang;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedComputerDesktop;

    protected static ?string $cluster = MasterCluster::class;

    protected static ?string $recordTitleAttribute = 'nama';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Total Barang';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Nama/Tipe')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(100)
                    ->validationMessages([
                        'unique' => 'Nama/Tipe Barang sudah ada.',
                        'required' => 'Nama/Tipe Barang wajib diisi.',
                        'max_length' => 'Nama/Tipe Barang maksimal :max karakter.',
                    ])
                    ->columnSpanFull(),

                Select::make('jenis_id')
                    ->label('Jenis')
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->relationship('jenis', 'nama')
                    ->createOptionForm([
                        TextInput::make('nama')
                            ->label('Jenis Barang')
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(40) 
                            ->validationMessages([
                                'required' => 'Jenis Barang wajib diisi.',
                                'max.length' => 'Jenis Barang maksimal :max karakter.',
                                'unique' => 'Jenis Barang sudah ada.',
                            ])
                    ])
                    ->createOptionAction(
                        fn (Action $action) => $action->modalWidth('md')->modalHeading('Tambah Jenis')
                    )
                    ->required()
                    ->validationMessages([
                        'required' => 'Jenis Barang wajib dipilih.',
                    ])
                    ->columnSpanFull(),

                Select::make('merek_id')
                    ->label('Merek')
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->relationship('merek', 'nama')
                    ->createOptionForm([
                        TextInput::make('nama')
                            ->label('Merek Barang')
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(50) 
                            ->validationMessages([
                                'required' => 'Jenis Barang wajib diisi.',
                                'max_length' => 'Jenis Barang maksimal :max karakter.',
                                'unique' => 'Jenis Barang sudah ada.',
                            ])
                    ])
                    ->createOptionAction(
                        fn (Action $action) => $action->modalWidth('md')->modalHeading('Tambah Merek')
                    )
                    ->required()
                    ->validationMessages([
                        'required' => 'Merek Barang wajib dipilih.',
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->defaultSort('nama', 'asc')
            ->columns([
                TextColumn::make('no')
                    ->label('No.')
                    ->rowIndex()
                    ->width('70px'),

                TextColumn::make('nama')
                    ->label('Nama/Tipe')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('jenis.nama')
                    ->label('Jenis')
                    ->sortable(),

                TextColumn::make('merek.nama')
                    ->label('Merek')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
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
            'index' => ManageBarangs::route('/'),
        ];
    }
}
