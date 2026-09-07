<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $fillable = [
        'name', 'email', 'seats', 'rsvp_status', 'rsvp_note',
        'dietary', 'unlocked_at', 'unlock_count', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'seats' => 'integer',
            'unlocked_at' => 'datetime',
            'unlock_count' => 'integer',
        ];
    }

    public static function findByEmail(string $email): ?self
    {
        return static::whereRaw('lower(email) = ?', [mb_strtolower(trim($email))])->first();
    }

    public function recordUnlock(): void
    {
        $this->forceFill([
            'unlocked_at' => now(),
            'unlock_count' => $this->unlock_count + 1,
        ])->save();
    }

    /** The line under the header: "We've saved 2 seats for you". */
    public function seatLine(): string
    {
        return $this->seats === 1
            ? "We've saved a seat for you"
            : "We've saved {$this->seats} seats for you";
    }
}
