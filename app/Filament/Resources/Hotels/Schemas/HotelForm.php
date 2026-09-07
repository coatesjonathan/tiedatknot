<?php

namespace App\Filament\Resources\Hotels\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class HotelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->label('Eyebrow')
                    ->placeholder('Our favourite')
                    ->required()
                    ->maxLength(255),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                RichEditor::make('description')
                    ->columnSpanFull(),
                TextInput::make('rate')
                    ->label('Rate')
                    ->placeholder('EUR 180 / night')
                    ->maxLength(255),
                TextInput::make('rooms_held')
                    ->label('Rooms held')
                    ->placeholder('12 rooms held')
                    ->maxLength(255),
                DatePicker::make('release_date')
                    ->label('Rooms released on'),
                TextInput::make('booking_url')
                    ->label('Booking link')
                    ->url()
                    ->maxLength(255),
                FileUpload::make('image_path')
                    ->label('Photo')
                    ->image()
                    ->imageEditor()
                    ->directory('wedding/hotels')
                    ->columnSpanFull(),
                Toggle::make('is_published')
                    ->label('Show on the invitation')
                    ->default(true),
            ]);
    }
}
