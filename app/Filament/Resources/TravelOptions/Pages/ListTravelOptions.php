<?php

namespace App\Filament\Resources\TravelOptions\Pages;

use App\Filament\Resources\TravelOptions\TravelOptionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTravelOptions extends ListRecords
{
    protected static string $resource = TravelOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
