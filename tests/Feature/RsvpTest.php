<?php

use App\Models\Guest;
use App\Models\Setting;

beforeEach(function () {
    Setting::current();
});

function guest(int $seats = 2): Guest
{
    return Guest::create(['name' => 'Sam', 'email' => 'sam@example.test', 'seats' => $seats]);
}

it('records an acceptance with the whole party', function () {
    $guest = guest();

    $this->withSession(['guest_id' => $guest->id])
        ->post('/rsvp', [
            'attending' => true,
            'party' => [
                ['name' => 'Sam', 'dietary' => 'No nuts'],
                ['name' => 'Alex Rivers', 'dietary' => ''],
            ],
            'rsvp_note' => 'Arriving the day before.',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $guest->refresh();

    expect($guest)
        ->rsvp_status->toBe('attending')
        ->attending_count->toBe(2)
        ->rsvp_note->toBe('Arriving the day before.')
        ->rsvp_submitted_at->not->toBeNull();

    expect($guest->attendees)->toHaveCount(2);
    expect($guest->attendees[0])->name->toBe('Sam')->dietary->toBe('No nuts');
    expect($guest->attendees[1])->name->toBe('Alex Rivers')->dietary->toBeNull();
    expect($guest->dietary_summary)->toBe('Sam — No nuts');
    expect($guest->party_names)->toBe('Sam, Alex Rivers');
});

it('records a decline without needing a party', function () {
    $guest = guest();

    $this->withSession(['guest_id' => $guest->id])
        ->post('/rsvp', ['attending' => false, 'rsvp_note' => 'Sorry to miss it.'])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $guest->refresh();

    expect($guest)
        ->rsvp_status->toBe('declined')
        ->attending_count->toBe(0)
        ->rsvp_note->toBe('Sorry to miss it.');

    expect($guest->attendees)->toHaveCount(0);
});

it('replaces the party when a guest changes their reply', function () {
    $guest = guest(3);

    $this->withSession(['guest_id' => $guest->id])
        ->post('/rsvp', ['attending' => true, 'party' => [['name' => 'Sam', 'dietary' => 'No nuts']]]);

    $this->withSession(['guest_id' => $guest->id])
        ->post('/rsvp', [
            'attending' => true,
            'party' => [['name' => 'Sam', 'dietary' => null], ['name' => 'Alex', 'dietary' => 'Vegan']],
        ])
        ->assertSessionHasNoErrors();

    $guest->refresh();

    expect($guest->attendees)->toHaveCount(2);
    expect($guest->attending_count)->toBe(2);
    expect($guest->dietary_summary)->toBe('Alex — Vegan');
});

it('refuses a party larger than the seats we saved', function () {
    $guest = guest(1);

    $this->withSession(['guest_id' => $guest->id])
        ->post('/rsvp', [
            'attending' => true,
            'party' => [['name' => 'Sam'], ['name' => 'Alex']],
        ])
        ->assertSessionHasErrors('party');

    expect($guest->fresh()->rsvp_status)->toBe('pending');
});

it('needs a name for everyone coming', function () {
    $guest = guest();

    $this->withSession(['guest_id' => $guest->id])
        ->post('/rsvp', ['attending' => true, 'party' => [['name' => '', 'dietary' => 'No nuts']]])
        ->assertSessionHasErrors('party.0.name');
});

it('needs an answer', function () {
    $guest = guest();

    $this->withSession(['guest_id' => $guest->id])
        ->post('/rsvp', ['party' => [['name' => 'Sam']]])
        ->assertSessionHasErrors('attending');
});

it('refuses a reply from someone who has not unlocked', function () {
    $guest = guest();

    $this->post('/rsvp', ['attending' => true, 'party' => [['name' => 'Sam']]])
        ->assertForbidden();

    expect($guest->fresh()->rsvp_status)->toBe('pending');
});

it('sends the saved reply back to the invitation page', function () {
    $guest = guest();
    $guest->recordRsvp(true, [['name' => 'Sam', 'dietary' => 'No nuts']], 'See you there.');

    $this->withSession(['guest_id' => $guest->id])
        ->get('/')
        ->assertOk()
        ->assertSee('No nuts')
        ->assertSee('See you there.');
});
