<?php

namespace App\Filament\Resources\GalleryImages\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GalleryImageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('image_path')
                    ->label('Photo')
                    ->image()
                    ->imageEditor()
                    ->automaticallyResizeImagesMode('contain')
                    ->automaticallyResizeImagesToWidth('2000')
                    ->automaticallyResizeImagesToHeight('2000')
                    ->automaticallyUpscaleImagesWhenResizing(false)
                    ->helperText('Crop with the editor if you like. Photos are scaled down to 2000px on the longest side as they upload.')
                    ->directory('wedding/gallery')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('caption')
                    ->maxLength(255),
            ]);
    }
}
