<?php

namespace App\Filament\Resources\Guests\Pages;

use App\Filament\Exports\GuestExporter;
use App\Filament\Imports\GuestImporter;
use App\Filament\Resources\Guests\GuestResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListGuests extends ListRecords
{
    protected static string $resource = GuestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(GuestImporter::class)
                ->label('Import CSV'),
            ExportAction::make()
                ->exporter(GuestExporter::class)
                ->label('Export CSV'),
            CreateAction::make()->label('Add guest'),
        ];
    }
}
