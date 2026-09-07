<?php

namespace App\Filament\Resources\SiteSections;

use App\Filament\Resources\SiteSections\Pages\ListSiteSections;
use App\Filament\Resources\SiteSections\Schemas\SiteSectionForm;
use App\Filament\Resources\SiteSections\Tables\SiteSectionsTable;
use App\Models\SiteSection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SiteSectionResource extends Resource
{
    protected static ?string $model = SiteSection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Sections & menu';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'section';

    protected static ?string $recordTitleAttribute = 'label';

    public static function form(Schema $schema): Schema
    {
        return SiteSectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SiteSectionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /** The sections are fixed by the page itself — they are configured, not created. */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSiteSections::route('/'),
        ];
    }
}
