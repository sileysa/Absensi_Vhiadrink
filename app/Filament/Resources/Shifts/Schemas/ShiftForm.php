<?php

namespace App\Filament\Resources\Shifts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ShiftForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TimePicker::make('checkin_start')
                    ->required(),
                TimePicker::make('checkin_end')
                    ->required(),
                TimePicker::make('checkout_time')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
