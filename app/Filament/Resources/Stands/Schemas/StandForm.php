<?php

namespace App\Filament\Resources\Stands\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class StandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Stand')
                    ->required()
                    ->maxLength(255),
                TextInput::make('region')
                    ->label('Daerah')
                    ->required()
                    ->maxLength(255),
                Textarea::make('address')
                    ->label('Alamat')
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('checkout_time')
                    ->label('Waktu Pulang (HH:mm)')
                    ->placeholder('17:00')
                    ->helperText('Masukkan waktu pulang dalam format HH:mm (contoh: 17:00)')
                    ->regex('/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/')
                    ->maxLength(5),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }
}
