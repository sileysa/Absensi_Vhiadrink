<?php

namespace App\Filament\Resources\Attendances\Tables;

use App\Enums\AttendanceType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('attended_at', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('stand.name')
                    ->label('Stand')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('stand.region')
                    ->label('Daerah')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (AttendanceType $state): string => $state->label())
                    ->color(fn (AttendanceType $state): string => match ($state) {
                        AttendanceType::CheckIn => 'success',
                        AttendanceType::CheckOut => 'warning',
                    }),
                TextColumn::make('attended_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(30)
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('stand_id')
                    ->label('Stand')
                    ->relationship('stand', 'name'),
                SelectFilter::make('type')
                    ->label('Tipe')
                    ->options(collect(AttendanceType::cases())->mapWithKeys(
                        fn (AttendanceType $type) => [$type->value => $type->label()]
                    )->all()),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
