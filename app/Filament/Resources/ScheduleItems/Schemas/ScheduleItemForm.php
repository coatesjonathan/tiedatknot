<?php

namespace App\Filament\Resources\ScheduleItems\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class ScheduleItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('time')
                    ->label('Time')
                    ->placeholder('18:00')
                    ->required()
                    ->maxLength(20),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('detail')
                    ->label('Detail line')
                    ->maxLength(255)
                    ->columnSpanFull(),
                Toggle::make('is_published')
                    ->label('Show on the invitation')
                    ->default(true),
            ]);
    }
}
