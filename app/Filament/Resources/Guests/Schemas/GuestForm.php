<?php

namespace App\Filament\Resources\Guests\Schemas;

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
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(20)
                            ->default(1),
                    ]),

                Section::make('Reply')
                    ->description('Record replies here as they come in by email.')
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
                        Textarea::make('dietary')
                            ->label('Dietary notes')
                            ->rows(2),
                        Textarea::make('rsvp_note')
                            ->label('Their reply')
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
