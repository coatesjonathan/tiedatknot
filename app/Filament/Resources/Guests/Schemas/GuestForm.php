<?php

namespace App\Filament\Resources\Guests\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GuestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Invitation')
                    ->description('The address the invitation was sent to unlocks the site.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Display name')
                            ->helperText('Used in the greeting — "Dear Sam", "Dear the Coates family".')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('seats')
                            ->label('Seats saved')
                            ->helperText('How many people this invitation covers.')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(20)
                            ->default(1),
                    ]),

                Section::make('Reply')
                    ->description('Replies arrive from the site — edit here if someone tells you in person.')
                    ->columns(2)
                    ->schema([
                        Select::make('rsvp_status')
                            ->label('RSVP')
                            ->options([
                                'pending' => 'Awaiting reply',
                                'attending' => 'Attending',
                                'declined' => 'Declined',
                            ])
                            ->default('pending')
                            ->required(),
                        TextInput::make('attending_count')
                            ->label('Seats taken')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(20)
                            ->placeholder('Not replied yet'),
                        Repeater::make('attendees')
                            ->label('Who is coming')
                            ->relationship()
                            ->orderColumn('sort_order')
                            ->columns(2)
                            ->addActionLabel('Add someone')
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(120),
                                TextInput::make('dietary')
                                    ->label('Dietary requirements')
                                    ->maxLength(500),
                            ])
                            ->columnSpanFull(),
                        Textarea::make('rsvp_note')
                            ->label('Their message')
                            ->rows(3)
                            ->columnSpanFull(),
                        Textarea::make('notes')
                            ->label('Our notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
