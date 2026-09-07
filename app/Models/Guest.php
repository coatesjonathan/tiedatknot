<?php

namespace App\Models;

use App\Support\SiteCopy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Guest extends Model
{
    protected $fillable = [
        'name', 'email', 'seats', 'attending_count', 'rsvp_status',
        'rsvp_note', 'rsvp_submitted_at', 'unlocked_at', 'unlock_count', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'seats' => 'integer',
            'attending_count' => 'integer',
            'rsvp_submitted_at' => 'datetime',
            'unlocked_at' => 'datetime',
            'unlock_count' => 'integer',
        ];
    }

    /** Everyone coming under this invitation — the named guest and their plus ones. */
    public function attendees(): HasMany
    {
        return $this->hasMany(RsvpAttendee::class)->orderBy('sort_order');
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

    /**
     * Store a reply made on the site. The party replaces whatever was there
     * before, so a guest can come back and correct it; declining empties it.
     *
     * @param  array<int, array{name: string, dietary: ?string}>  $party
     */
    public function recordRsvp(bool $attending, array $party, ?string $note): void
    {
        $this->forceFill([
            'rsvp_status' => $attending ? 'attending' : 'declined',
            'attending_count' => $attending ? count($party) : 0,
            'rsvp_note' => $note,
            'rsvp_submitted_at' => now(),
        ])->save();

        $this->attendees()->delete();

        if (! $attending) {
            return;
        }

        $this->attendees()->createMany(
            collect($party)->values()->map(fn (array $person, int $index) => [
                'name' => $person['name'],
                'dietary' => $person['dietary'] ?: null,
                'sort_order' => $index,
            ])->all()
        );

        $this->load('attendees');
    }

    /** "Sam — no nuts; Alex — vegetarian". Used by the admin table and the export. */
    protected function dietarySummary(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->attendees
            ->filter(fn (RsvpAttendee $person) => filled($person->dietary))
            ->map(fn (RsvpAttendee $person) => $person->name.' — '.$person->dietary)
            ->join('; ') ?: null);
    }

    /** "Sam, Alex" — the party as one line. */
    protected function partyNames(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->attendees
            ->pluck('name')
            ->join(', ') ?: null);
    }

    /** The party in the shape the invitation form binds to. */
    public function partyForForm(): Collection
    {
        return $this->attendees
            ->map(fn (RsvpAttendee $person) => ['name' => $person->name, 'dietary' => $person->dietary ?? ''])
            ->values();
    }

    /** The line under the header: "We've saved 2 seats for you". */
    public function seatLine(): string
    {
        return $this->seats === 1
            ? SiteCopy::line('header.seats_one')
            : SiteCopy::line('header.seats_many', ['count' => $this->seats]);
    }
}
