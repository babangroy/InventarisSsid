<?php

namespace App\Providers;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Tables\Table;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        TextInput::configureUsing(function (TextInput $input): void {
            $input->autocomplete(false);
        });

        FilamentColor::register([
            'danger' => Color::Red,
            'gray' => Color::Zinc,
            'info' => Color::Blue,
            'success' => Color::Green,
            'warning' => Color::Amber,
        ]);

        Table::configureUsing(function (Table $table): void {
            $table->defaultPaginationPageOption(25);
        });
    }
}
