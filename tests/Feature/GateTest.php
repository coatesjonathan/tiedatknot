<?php

use App\Models\Guest;
use App\Models\Setting;

beforeEach(function () {
    Setting::current();
});

it('shows the gate without any invitation content', function () {
    $guest = Guest::create(['name' => 'Sam', 'email' => 'sam@example.test', 'seats' => 2]);

    $this->get('/')
        ->assertOk()
        ->assertSee('Invitation') // the Inertia page component
        ->assertDontSee('unlocked&quot;:true', escape: false)
        ->assertDontSee($guest->name);
});

it('unlocks for an address on the list, whatever the casing', function () {
    $guest = Guest::create(['name' => 'Sam', 'email' => 'sam@example.test', 'seats' => 2]);

    $this->post('/unlock', ['email' => '  SAM@Example.test '])
        ->assertRedirect()
        ->assertSessionHas('guest_id', $guest->id);

    expect($guest->fresh())
        ->unlock_count->toBe(1)
        ->unlocked_at->not->toBeNull();
});

it('refuses an address that is not on the list', function () {
    $this->post('/unlock', ['email' => 'nobody@example.test'])
        ->assertSessionHasErrors(['email' => "We can't find that address on our list — try the one the invitation came to."]);

    expect(session()->has('guest_id'))->toBeFalse();
});

it('refuses something that is not an email address', function () {
    $this->post('/unlock', ['email' => 'not-an-email'])
        ->assertSessionHasErrors(['email' => 'That does not look like an email address.']);
});

it('serves the invitation content once unlocked', function () {
    $guest = Guest::create(['name' => 'Sam', 'email' => 'sam@example.test', 'seats' => 2]);

    $this->withSession(['guest_id' => $guest->id])
        ->get('/')
        ->assertOk()
        ->assertSee('Dear Sam')
        ->assertSee("We've saved 2 seats for you", escape: false);
});

it('locks again when the guest asks to replay', function () {
    $guest = Guest::create(['name' => 'Sam', 'email' => 'sam@example.test', 'seats' => 1]);

    $this->withSession(['guest_id' => $guest->id])
        ->delete('/unlock')
        ->assertRedirect();

    expect(session()->has('guest_id'))->toBeFalse();
});
