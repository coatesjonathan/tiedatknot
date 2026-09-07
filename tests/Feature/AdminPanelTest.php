<?php

use App\Models\Setting;
use App\Models\User;

beforeEach(function () {
    Setting::current();
    $this->actingAs(User::factory()->create());
});

it('renders every panel page', function (string $path) {
    $this->get($path)->assertOk();
})->with([
    '/admin',
    '/admin/guests',
    '/admin/guests/create',
    '/admin/schedule-items',
    '/admin/travel-options',
    '/admin/hotels',
    '/admin/highlights',
    '/admin/notes',
    '/admin/faqs',
    '/admin/gallery-images',
    '/admin/manage-settings',
    '/admin/manage-copy',
    '/admin/site-sections',
]);
