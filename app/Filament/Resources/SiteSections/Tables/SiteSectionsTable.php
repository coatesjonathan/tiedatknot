<?php

namespace App\Filament\Resources\SiteSections\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class SiteSectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->paginated(false)
            ->columns([
                TextColumn::make('label')->label('Menu wording'),
                TextColumn::make('anchor')->label('Section')->color('gray'),
                ToggleColumn::make('is_published')->label('Section shown'),
                ToggleColumn::make('in_menu')->label('In the menu'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
