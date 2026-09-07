<?php

namespace App\Filament\Exports;

use App\Models\Guest;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Str;

class GuestExporter extends Exporter
{
    protected static ?string $model = Guest::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label('Display name'),
            ExportColumn::make('email')->label('Email address'),
            ExportColumn::make('seats')->label('Seats saved'),
            ExportColumn::make('attending_count')->label('Seats taken'),
            ExportColumn::make('rsvp_status')->label('RSVP'),
            ExportColumn::make('party_names')->label('Who is coming'),
            ExportColumn::make('dietary_summary')->label('Dietary requirements'),
            ExportColumn::make('rsvp_note')->label('Their message'),
            ExportColumn::make('rsvp_submitted_at')->label('Replied at'),
            ExportColumn::make('unlocked_at')->label('Opened at'),
            ExportColumn::make('unlock_count')->label('Opens'),
            ExportColumn::make('notes'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your guest export has completed and '.Str::of('row')->counted($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Str::of('row')->counted($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
