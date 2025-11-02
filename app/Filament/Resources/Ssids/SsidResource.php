<?php

namespace App\Filament\Resources\Ssids;

use App\Filament\Resources\Ssids\Pages\ManageSsids;
use App\Models\Ssid;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class SsidResource extends Resource
{
    protected static ?string $model = Ssid::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Wifi;

    protected static string | UnitEnum | null $navigationGroup = 'Management Wifi';

    protected static ?string $recordTitleAttribute = 'ssid';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Total SSID';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('ssid')
                    ->required()
                    ->validationMessages([
                        'required' => 'SSID tidak boleh kosong',
                    ])
                    ->columnSpanFull(),

                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->required()
                    ->validationMessages([
                        'required' => 'Password tidak boleh kosong',
                    ])
                    ->columnspanFull(),

                TextInput::make('lokasi')
                    ->required()
                    ->validationMessages([
                        'required' => 'Lokasi tidak boleh kosong'])
                    ->columnSpanFull(),

                Select::make('status')
                    ->options(['Aktif' => 'Aktif', 'Tidak Aktif' => 'Tidak aktif'])
                    ->default('Aktif')
                    ->required()
                    ->validationMessages([
                        'required' => 'Status harus dipilih',
                    ])
                    ->columnSpanFull()
                    ->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('ssid')
            ->columns([
                TextColumn::make('no')
                    ->label('No.')
                    ->rowIndex()
                    ->width('70px'),

                TextColumn::make('ssid')
                    ->label('SSID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('password')
                    ->icon(Heroicon::LockClosed)
                    ->iconColor(Color::Amber)
                    ->formatStateUsing(fn () => '••••••••'),

                TextColumn::make('lokasi')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime('d M Y H:i'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Aktif' => 'success',
                        'Tidak Aktif' => 'danger',
                    })
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Aktif' => 'Aktif',
                        'Tidak Aktif' => 'Tidak Aktif',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Ubah')
                    ->modalWidth('md'),
                DeleteAction::make()
                    ->label('Hapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSsids::route('/'),
        ];
    }
}
