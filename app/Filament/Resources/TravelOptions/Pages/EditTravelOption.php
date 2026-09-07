<?php

namespace App\Filament\Resources\TravelOptions\Pages;

use App\Filament\Resources\TravelOptions\TravelOptionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTravelOption extends EditRecord
{
    protected static string $resource = TravelOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
