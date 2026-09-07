<?php

namespace App\Filament\Resources\SiteSections\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SiteSectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->label('Menu wording')
                    ->helperText('Keep it short — the menu sits across the top of the page.')
                    ->required()
                    ->maxLength(40),

                Toggle::make('is_published')
                    ->label('Show this section')
                    ->helperText('Turn off while you are still gathering the details.')
                    ->default(true),

                Toggle::make('in_menu')
                    ->label('Show in the menu')
                    ->default(true),
            ]);
    }
}
