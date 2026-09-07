<?php

namespace App\Filament\Resources\Guests\Tables;

use App\Filament\Exports\GuestExporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class GuestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('seats')
                    ->label('Saved')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('attending_count')
                    ->label('Taking')
                    ->numeric()
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('rsvp_status')
                    ->label('RSVP')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'attending' => 'Attending',
                        'declined' => 'Declined',
                        default => 'Awaiting reply',
                    })
                    ->color(fn (string $state) => match ($state) {
                        'attending' => 'success',
                        'declined' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('party_names')
                    ->label('Who is coming')
                    ->placeholder('—')
                    ->wrap()
                    ->toggleable(),
                TextColumn::make('dietary_summary')
                    ->label('Dietary')
                    ->placeholder('None given')
                    ->wrap()
                    ->toggleable(),
                TextColumn::make('unlocked_at')
                    ->label('Opened')
                    ->dateTime('j M Y, H:i')
                    ->placeholder('Not yet')
                    ->sortable(),
                TextColumn::make('unlock_count')
                    ->label('Opens')
                    ->numeric()
                    ->sortable(),
            ])
            ->defaultSort('name')
            ->filters([
                SelectFilter::make('rsvp_status')
                    ->label('RSVP')
                    ->options([
                        'pending' => 'Awaiting reply',
                        'attending' => 'Attending',
                        'declined' => 'Declined',
                    ]),
                TernaryFilter::make('rsvp_submitted_at')
                    ->label('Replied on the site')
                    ->nullable()
                    ->trueLabel('Replied online')
                    ->falseLabel('Not yet'),
                TernaryFilter::make('unlocked_at')
                    ->label('Opened the invitation')
                    ->nullable()
                    ->trueLabel('Has opened it')
                    ->falseLabel('Not yet'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportBulkAction::make()->exporter(GuestExporter::class),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No guests yet')
            ->emptyStateDescription('Import the guest list as a CSV of name, email and seats.');
    }
}
