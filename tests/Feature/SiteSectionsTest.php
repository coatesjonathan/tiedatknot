<?php

use App\Models\Guest;
use App\Models\Setting;
use App\Models\SiteSection;
use App\Support\PageSections;
use Illuminate\Testing\TestResponse;

beforeEach(function () {
    Setting::current();
});

function unlockedGuest(): Guest
{
    return Guest::create(['name' => 'Sam', 'email' => 'sam@example.test', 'seats' => 2]);
}

function viewInvitation(): TestResponse
{
    return test()->withSession(['guest_id' => unlockedGuest()->id])->get('/');
}

it('seeds a row for every section, shown and in the menu', function () {
    expect(SiteSection::count())->toBe(count(PageSections::ALL));
    expect(SiteSection::orderBy('sort_order')->pluck('anchor')->all())->toBe(PageSections::anchors());
    expect(SiteSection::where('is_published', true)->count())->toBe(count(PageSections::ALL));
    expect(SiteSection::where('in_menu', true)->count())->toBe(count(PageSections::ALL));
});

it('marks a section off when it is switched off', function () {
    SiteSection::where('anchor', 'the-day')->update(['is_published' => false]);

    viewInvitation()
        ->assertOk()
        ->assertSee('"the-day":false', escape: false)
        ->assertSee('"the-place":true', escape: false);
});

it('drops a switched-off section from the menu too', function () {
    SiteSection::where('anchor', 'the-day')->update(['is_published' => false, 'label' => 'Hidden section']);

    viewInvitation()
        ->assertOk()
        ->assertDontSee('Hidden section');
});

it('can keep a section but leave it out of the menu', function () {
    SiteSection::where('anchor', 'the-day')->update(['in_menu' => false, 'label' => 'Not in menu']);

    viewInvitation()
        ->assertOk()
        ->assertDontSee('Not in menu')
        ->assertSee('"the-day":true', escape: false);
});

it('sends the menu in order', function () {
    SiteSection::query()->delete();
    SiteSection::create(['anchor' => 'rsvp', 'label' => 'Second', 'sort_order' => 2]);
    SiteSection::create(['anchor' => 'the-day', 'label' => 'First', 'sort_order' => 1]);

    viewInvitation()
        ->assertOk()
        ->assertSeeInOrder(['First', 'Second']);
});

it('keeps the menu behind the gate', function () {
    SiteSection::where('anchor', 'the-day')->update(['label' => 'Secret link']);

    $this->get('/')
        ->assertOk()
        ->assertDontSee('Secret link');
});

it('only offers anchors the page actually renders', function () {
    $template = file_get_contents(resource_path('js/pages/Invitation.vue'));

    foreach (PageSections::anchors() as $anchor) {
        expect($template)->toContain('id="'.$anchor.'"');
        expect($template)->toContain("shown('".$anchor."')");
    }
});

it('has a label for every anchor and no duplicates', function () {
    expect(PageSections::anchors())->toBe(array_unique(PageSections::anchors()));

    foreach (PageSections::ALL as $anchor => $label) {
        expect($anchor)->toMatch('/^[a-z0-9-]+$/');
        expect($label)->not->toBeEmpty();
    }
});

it('gives an unlocked guest the reply shortcut wording and their reply status', function () {
    viewInvitation()
        ->assertOk()
        ->assertSee('RSVP now')
        ->assertSee('Change your reply')
        ->assertSee('"status":"pending"', escape: false);
});

it('keeps the reply shortcut wording behind the gate', function () {
    $this->get('/')
        ->assertOk()
        ->assertDontSee('RSVP now')
        ->assertDontSee('Change your reply');
});
