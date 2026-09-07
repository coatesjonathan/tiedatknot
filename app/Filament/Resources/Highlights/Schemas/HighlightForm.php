<?php

namespace App\Filament\Resources\Highlights\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class HighlightForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                RichEditor::make('body')
                    ->required()
                    ->columnSpanFull(),
                Toggle::make('is_published')
                    ->label('Show on the invitation')
                    ->default(true),
            ]);
    }
}
