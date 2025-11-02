<?php

namespace App\Filament\Clusters\Master\Resources\Users;

use App\Filament\Clusters\Master\MasterCluster;
use App\Filament\Clusters\Master\Resources\Users\Pages\ManageUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $modelLabel = 'Pengguna';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserPlus;

    protected static string | UnitEnum | null $navigationGroup = 'Akun';

    protected static ?string $cluster = MasterCluster::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('username')
                    ->label('Nama Pengguna')
                    ->required()
                    ->validationMessages([
                        'required' => 'Nama pengguna harus di isi'
                    ])
                    ->columnSpanFull(),

                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->validationMessages([
                        'required' => 'Nama Lengkap harus di isi'
                    ])
                    ->columnSpanFull(),

                TextInput::make('email')
                    ->label('Alamat Email')
                    ->email()
                    ->required()
                    ->validationMessages([
                        'required' => 'Alamat email harus di isi',
                        'email' => 'Harus berupa email (@)'
                    ])
                    ->columnSpanFull(),

                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->validationMessages([
                        'required' => 'Password harus di isi'
                    ])
                    ->dehydrated(fn ($state): bool => filled($state))
                    ->afterStateHydrated(fn (callable $set) => $set('password', ''))
                    ->placeholder(fn (string $operation): ?string =>
                        $operation === 'edit'
                            ? 'Kosongkan jika tidak ingin mengubah password'
                            : null
                    )
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('name', 'asc')
            ->columns([
                TextColumn::make('no')
                    ->label('No.')
                    ->rowIndex()
                    ->width('70px'),

                TextColumn::make('username')
                    ->label('Nama Pengguna')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Alamat Email')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth('md'),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUsers::route('/'),
        ];
    }
}
