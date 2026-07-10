<?php

namespace App\Filament\Resources\Stands;

use App\Filament\Resources\Stands\Pages\CreateStand;
use App\Filament\Resources\Stands\Pages\EditStand;
use App\Filament\Resources\Stands\Pages\ListStands;
use App\Filament\Resources\Stands\Schemas\StandForm;
use App\Filament\Resources\Stands\Tables\StandsTable;
use App\Models\Stand;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StandResource extends Resource
{
    protected static ?string $model = Stand::class;

    protected static ?string $navigationLabel = 'Stand';

    protected static ?string $modelLabel = 'Stand';

    protected static ?string $pluralModelLabel = 'Stand';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return StandForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StandsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStands::route('/'),
            'create' => CreateStand::route('/create'),
            'edit' => EditStand::route('/{record}/edit'),
        ];
    }
}
