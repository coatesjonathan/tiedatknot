<?php

namespace App\Filament\Resources\Notes\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class NoteForm
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
                    ->helperText('Use the link button for things like your honeymoon fund.')
                    ->columnSpanFull(),
                Toggle::make('is_published')
                    ->label('Show on the invitation')
                    ->default(true),
            ]);
    }
}
