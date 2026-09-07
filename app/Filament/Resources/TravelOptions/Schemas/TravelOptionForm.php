<?php

namespace App\Filament\Resources\TravelOptions\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TravelOptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->label('Eyebrow')
                    ->placeholder('Option one')
                    ->required()
                    ->maxLength(255),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                RichEditor::make('body')
                    ->required()
                    ->columnSpanFull(),
                RichEditor::make('footnote')
                    ->helperText('Use the link button to point at a booking site.')
                    ->rows(2)
                    ->columnSpanFull(),
                Toggle::make('is_published')
                    ->label('Show on the invitation')
                    ->default(true),
            ]);
    }
}
