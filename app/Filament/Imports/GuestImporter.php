<?php

namespace App\Filament\Imports;

use App\Models\Guest;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class GuestImporter extends Importer
{
    protected static ?string $model = Guest::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Display name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('email')
                ->label('Email address')
                ->requiredMapping()
                ->rules(['required', 'email', 'max:255']),
            ImportColumn::make('seats')
                ->numeric()
                ->rules(['integer', 'min:1', 'max:20'])
                ->fillRecordUsing(fn (Guest $record, ?string $state) => $record->seats = (int) ($state ?: 1)),
        ];
    }

    /** Match on the normalised address so a re-import updates rather than duplicates. */
    public function resolveRecord(): Guest
    {
        return Guest::firstOrNew([
            'email' => mb_strtolower(trim($this->data['email'])),
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your guest import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
