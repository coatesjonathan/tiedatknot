<?php

namespace App\Filament\Widgets;

use App\Models\Guest;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentUnlocks extends TableWidget
{
    protected static ?string $heading = 'Recently opened';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Guest::query()->whereNotNull('unlocked_at'))
            ->defaultSort('unlocked_at', 'desc')
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email')->label('Email address')->color('gray'),
                TextColumn::make('seats'),
                TextColumn::make('unlocked_at')->label('Last opened')->since(),
                TextColumn::make('unlock_count')->label('Opens'),
            ])
            ->emptyStateHeading('Nobody has opened the invitation yet');
    }
}
