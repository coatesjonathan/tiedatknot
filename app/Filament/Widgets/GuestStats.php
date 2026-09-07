<?php

namespace App\Filament\Widgets;

use App\Models\Guest;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GuestStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $invited = Guest::count();
        $unlocked = Guest::whereNotNull('unlocked_at')->count();
        $attending = Guest::where('rsvp_status', 'attending')->count();
        $declined = Guest::where('rsvp_status', 'declined')->count();
        $pending = Guest::where('rsvp_status', 'pending')->count();
        $seats = Guest::where('rsvp_status', 'attending')->sum('attending_count');

        return [
            Stat::make('Invited', $invited)
                ->description($unlocked.' have opened the invitation')
                ->color('gray'),
            Stat::make('Attending', $attending)
                ->description($seats.' '.str('seat')->plural($seats).' accepted')
                ->color('success'),
            Stat::make('Awaiting reply', $pending)
                ->description($declined.' declined')
                ->color($pending > 0 ? 'warning' : 'success'),
        ];
    }
}
