<?php

namespace App\Filament\Resources\TravelOptions;

use App\Filament\Resources\TravelOptions\Pages\CreateTravelOption;
use App\Filament\Resources\TravelOptions\Pages\EditTravelOption;
use App\Filament\Resources\TravelOptions\Pages\ListTravelOptions;
use App\Filament\Resources\TravelOptions\Schemas\TravelOptionForm;
use App\Filament\Resources\TravelOptions\Tables\TravelOptionsTable;
use App\Models\TravelOption;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;
use Filament\Tables\Table;

class TravelOptionResource extends Resource
{
    protected static ?string $model = TravelOption::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Travel';

    protected static ?string $recordTitleAttribute = null;

    public static function form(Schema $schema): Schema
    {
        return TravelOptionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TravelOptionsTable::configure($table);
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
            'index' => ListTravelOptions::route('/'),
            'create' => CreateTravelOption::route('/create'),
            'edit' => EditTravelOption::route('/{record}/edit'),
        ];
    }
}
