<?php

namespace App\Filament\Clusters\Master\Resources\Mereks;

use App\Filament\Clusters\Master\MasterCluster;
use App\Filament\Clusters\Master\Resources\Mereks\Pages\ManageMereks;
use App\Models\Merek;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class MerekResource extends Resource
{
    protected static ?string $model = Merek::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Bars3BottomLeft;

    protected static string | UnitEnum | null $navigationGroup = 'Referensi';

    protected static ?string $cluster = MasterCluster::class;

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Merek Barang')
                    ->unique(ignoreRecord: true)
                    ->maxLength(50)
                    ->required()
                    ->validationMessages([
                        'required' => 'Merek Barang wajib diisi.',
                        'max_length' => 'Merek Barang maksimal :max karakter.',
                        'unique' => 'Merek Barang sudah ada.',
                    ])
                    ->columnSpanFull(),
                    
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('merek')
            ->defaultSort('nama', 'asc')
            ->columns([
                TextColumn::make('no')
                    ->label('No.')
                    ->rowIndex()
                    ->width('70px'),

                TextColumn::make('nama')
                    ->label('Merek Barang')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Ubah')
                    ->modalWidth('md'),
                DeleteAction::make()
                    ->label('Hapus')
                    ->before(function (Merek $record, DeleteAction $action) {
                        if ($record->barangs()->exists()) {
                            Notification::make()
                                ->title('Gagal Menghapus')
                                ->body('Merek ini masih digunakan pada tabel Barang.')
                                ->danger()
                                ->duration(4000)
                                ->send();
                            $action->cancel();
                            return;
                        }
                    }),
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
            'index' => ManageMereks::route('/'),
        ];
    }
}
