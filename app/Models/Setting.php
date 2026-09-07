<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'wedding_date' => 'date',
            'rsvp_deadline' => 'date',
            'countdown_enabled' => 'boolean',
        ];
    }

    /** The singleton row. Created on first access so the panel always has something to edit. */
    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }
}
