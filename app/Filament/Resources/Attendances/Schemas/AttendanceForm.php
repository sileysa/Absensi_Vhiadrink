<?php

namespace App\Filament\Resources\Attendances\Schemas;

use App\Enums\AttendanceType;
use App\Models\Stand;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileInfo;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Karyawan')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->native(false),
                Select::make('stand_id')
                    ->label('Stand')
                    ->options(fn () => Stand::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->native(false),
                Select::make('type')
                    ->label('Tipe')
                    ->options(collect(AttendanceType::cases())->mapWithKeys(
                        fn (AttendanceType $type) => [$type->value => $type->label()]
                    )->all())
                    ->required()
                    ->native(false),
                DateTimePicker::make('attended_at')
                    ->label('Waktu Absensi')
                    ->default(now())
                    ->required(),
                Textarea::make('notes')
                    ->label('Catatan')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
