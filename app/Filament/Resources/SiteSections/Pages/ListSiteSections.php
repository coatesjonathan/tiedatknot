<?php

namespace App\Filament\Resources\SiteSections\Pages;

use App\Filament\Resources\SiteSections\SiteSectionResource;
use Filament\Resources\Pages\ListRecords;

class ListSiteSections extends ListRecords
{
    protected static string $resource = SiteSectionResource::class;

    public function getSubheading(): ?string
    {
        return 'Turn a section off while you are still working out the details — it disappears from the page and the menu.';
    }
}
