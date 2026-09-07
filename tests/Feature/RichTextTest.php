<?php

use App\Models\Faq;
use App\Models\Guest;
use App\Models\Setting;
use App\Support\RichText;

beforeEach(function () {
    Setting::current();
});

it('keeps the formatting the editor produces', function () {
    expect(RichText::html('<p>A <strong>walled</strong> garden with a <em>view</em>.</p>'))
        ->toBe('<p>A <strong>walled</strong> garden with a <em>view</em>.</p>');

    expect(RichText::html('<ul><li>One</li><li>Two</li></ul>'))
        ->toBe('<ul><li>One</li><li>Two</li></ul>');
});

it('strips anything that could run', function () {
    $dirty = '<p>Hello</p><script>alert(1)</script><img src=x onerror="alert(1)">';

    expect(RichText::html($dirty))
        ->not->toContain('script')
        ->not->toContain('onerror');
});

it('defuses a javascript link', function () {
    expect(RichText::html('<a href="javascript:alert(1)">tap</a>'))
        ->not->toContain('javascript:');
});

it('makes outgoing links safe', function () {
    expect(RichText::html('<a href="https://renfe.com">renfe</a>'))
        ->toContain('rel="noopener noreferrer"')
        ->toContain('target="_blank"');
});

it('treats an editor emptied by hand as nothing', function () {
    expect(RichText::html('<p></p>'))->toBeNull();
    expect(RichText::html('<p><br></p>'))->toBeNull();
    expect(RichText::html(null))->toBeNull();
    expect(RichText::html('  '))->toBeNull();
});

it('carries plain copy over to HTML, links and all', function () {
    $plain = "First line\nsecond line\n\nBook at [renfe.com](https://renfe.com).";

    expect(RichText::fromPlainText($plain))
        ->toBe('<p>First line<br>second line</p><p>Book at <a href="https://renfe.com" target="_blank" rel="noopener">renfe.com</a>.</p>');
});

it('escapes plain copy that looks like markup', function () {
    expect(RichText::fromPlainText('5 < 6 & <b>bold</b>'))
        ->toBe('<p>5 &lt; 6 &amp; &lt;b&gt;bold&lt;/b&gt;</p>');
});

it('turns HTML back into plain text', function () {
    expect(RichText::toPlainText('<p>One<br>two</p><p>Three</p>'))->toBe("One\ntwo\n\nThree");
});

it('sends sanitised HTML to an unlocked guest', function () {
    $guest = Guest::create(['name' => 'Sam', 'email' => 'sam@example.test', 'seats' => 2]);
    Faq::create([
        'question' => 'Plus ones?',
        'answer' => '<p>Ask <strong>us</strong>.</p><script>alert(1)</script>',
        'is_published' => true,
    ]);

    $response = $this->withSession(['guest_id' => $guest->id])->get('/')->assertOk();

    expect($response->getContent())
        ->toContain('Ask <strong>us<\/strong>.')
        ->not->toContain('alert(1)');
});
