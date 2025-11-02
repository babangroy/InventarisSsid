<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Auth\Pages\Login as BaseAuthLogin;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

class Login extends BaseAuthLogin
{
    protected function getEmailFormComponent(): TextInput
    {
        return TextInput::make('username')
            ->label('Nama Pengguna')
            ->required();
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'username' => $data['username'],
            'password' => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        Notification::make()
            ->title('Login gagal')
            ->body('Nama pengguna atau password salah.')
            ->danger()
            ->send();

        throw ValidationException::withMessages([
            'username' => 'Nama pengguna atau password salah.',
        ]);
    }
}