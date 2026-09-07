<?php

use App\Filament\Pages\ManageCopy;
use App\Models\Guest;
use App\Models\Setting;
use App\Models\SiteText;
use App\Models\User;
use App\Support\SiteCopy;

beforeEach(function () {
    Setting::current();
});

it('falls back to the default when nothing is stored', function () {
    expect(SiteText::count())->toBe(0);
    expect(SiteCopy::line('rsvp.heading'))->toBe('Will you be there?');
});

it('uses a stored override instead of the default', function () {
    SiteText::create(['key' => 'rsvp.heading', 'value' => 'Can you come?']);

    expect(SiteCopy::line('rsvp.heading'))->toBe('Can you come?');
});

it('ignores a blank override so the default keeps showing', function () {
    SiteText::create(['key' => 'rsvp.heading', 'value' => '']);

    expect(SiteCopy::line('rsvp.heading'))->toBe('Will you be there?');
});

it('fills placeholders', function () {
    expect(SiteCopy::line('header.seats_many', ['count' => 4]))
        ->toBe("We've saved 4 seats for you");
});

it('keeps every key in exactly one group with a default', function () {
    foreach (SiteCopy::definition() as $key => [$group, $label, $default, $rows]) {
        expect(array_key_exists($group, SiteCopy::GROUPS))->toBeTrue();
        expect($label)->not->toBeEmpty();
        expect($default)->not->toBeEmpty();
        expect($rows)->toBeGreaterThan(0);
    }
});

it('splits the copy so the gate only gets its own lines', function () {
    $gate = SiteCopy::forGate();
    $invitation = SiteCopy::forInvitation();

    expect($gate)->toHaveKey('gate.submit');
    expect($gate)->not->toHaveKey('rsvp.heading');
    expect($invitation)->toHaveKey('rsvp.heading');
    expect(array_intersect_key($gate, $invitation))->toBe([]);
    expect(count($gate) + count($invitation))->toBe(count(SiteCopy::definition()));
});

it('never sends invitation wording to a locked visitor', function () {
    SiteText::create(['key' => 'rsvp.heading', 'value' => 'Secret question']);

    $this->get('/')
        ->assertOk()
        ->assertDontSee('Secret question')
        ->assertSee('Break the seal', escape: false);
});

it('sends the edited wording to an unlocked guest', function () {
    $guest = Guest::create(['name' => 'Sam', 'email' => 'sam@example.test', 'seats' => 2]);
    SiteText::create(['key' => 'rsvp.heading', 'value' => 'Can you come?']);

    $this->withSession(['guest_id' => $guest->id])
        ->get('/')
        ->assertOk()
        ->assertSee('Can you come?', escape: false);
});

it('uses the edited wording for the greeting and seat line', function () {
    $guest = Guest::create(['name' => 'Sam', 'email' => 'sam@example.test', 'seats' => 2]);
    SiteText::create(['key' => 'header.greeting', 'value' => 'Hello :name']);
    SiteText::create(['key' => 'header.seats_many', 'value' => ':count places held']);

    $this->withSession(['guest_id' => $guest->id])
        ->get('/')
        ->assertOk()
        ->assertSee('Hello Sam')
        ->assertSee('2 places held');
});

it('uses the edited wording for the gate error', function () {
    SiteText::create(['key' => 'gate.error_unknown', 'value' => 'Not on our list, sorry.']);

    $this->post('/unlock', ['email' => 'nobody@example.test'])
        ->assertSessionHasErrors(['email' => 'Not on our list, sorry.']);
});

it('uses the edited wording for an RSVP error', function () {
    $guest = Guest::create(['name' => 'Sam', 'email' => 'sam@example.test', 'seats' => 1]);
    SiteText::create(['key' => 'rsvp.error_party_max', 'value' => 'Only :count, sorry.']);

    $this->withSession(['guest_id' => $guest->id])
        ->post('/rsvp', ['attending' => true, 'party' => [['name' => 'Sam'], ['name' => 'Alex']]])
        ->assertSessionHasErrors(['party' => 'Only 1, sorry.']);
});

it('saves an edited line and drops one left at its default', function () {
    $this->actingAs(User::factory()->create());

    Livewire\Livewire::test(ManageCopy::class)
        ->assertOk()
        ->set('data.rsvp__heading', 'Can you come?')
        ->set('data.section__day', 'The day')   // unchanged from the default
        ->call('save');

    expect(SiteCopy::line('rsvp.heading'))->toBe('Can you come?');
    expect(SiteText::where('key', 'rsvp.heading')->exists())->toBeTrue();
    expect(SiteText::where('key', 'section.day')->exists())->toBeFalse();
});

it('treats a cleared field as back to the default', function () {
    $this->actingAs(User::factory()->create());
    SiteText::create(['key' => 'rsvp.heading', 'value' => 'Can you come?']);

    Livewire\Livewire::test(ManageCopy::class)
        ->set('data.rsvp__heading', '')
        ->call('save');

    expect(SiteText::where('key', 'rsvp.heading')->exists())->toBeFalse();
    expect(SiteCopy::line('rsvp.heading'))->toBe('Will you be there?');
});

it('resets every line back to the defaults', function () {
    $this->actingAs(User::factory()->create());
    SiteText::create(['key' => 'rsvp.heading', 'value' => 'Can you come?']);
    SiteText::create(['key' => 'section.day', 'value' => 'Running order']);

    Livewire\Livewire::test(ManageCopy::class)->call('resetToDefaults');

    expect(SiteText::count())->toBe(0);
    expect(SiteCopy::line('rsvp.heading'))->toBe('Will you be there?');
});

it('prefills the form with the wording currently in use', function () {
    $this->actingAs(User::factory()->create());
    SiteText::create(['key' => 'rsvp.heading', 'value' => 'Can you come?']);

    Livewire\Livewire::test(ManageCopy::class)
        ->assertSet('data.rsvp__heading', 'Can you come?')
        ->assertSet('data.section__day', 'The day');
});
