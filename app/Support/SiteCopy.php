<?php

namespace App\Support;

use App\Models\SiteText;
use Illuminate\Support\Collection;

/**
 * Every word on the invitation that isn't guest data or a content list.
 *
 * The defaults here are the source of truth: a key with no row in `site_texts`
 * renders its default, so the site reads correctly on a fresh install and the
 * couple can override any single line without touching the rest.
 *
 * `:name` placeholders are substituted at render time.
 */
class SiteCopy
{
    /** Groups whose wording the gate needs before a guest has unlocked. */
    private const GATE_GROUPS = ['gate'];

    public const GROUPS = [
        'gate' => 'The gate',
        'header' => 'The header',
        'sections' => 'Section headings',
        'rsvp' => 'The RSVP form',
        'footer' => 'The footer',
    ];

    /**
     * key => [group, label, default, rows]
     *
     * `rows` of 1 renders a single-line input; anything higher a textarea.
     *
     * @return array<string, array{0: string, 1: string, 2: string, 3: int}>
     */
    public static function definition(): array
    {
        return [
            // ---- The gate ------------------------------------------------
            'gate.intro' => ['gate', 'Invitation to enter', 'Sealed for you. Enter the email your invitation came to.', 2],
            'gate.email_placeholder' => ['gate', 'Email box placeholder', 'you@example.com', 1],
            'gate.submit' => ['gate', 'Button', 'Break the seal', 1],
            'gate.help_prefix' => ['gate', 'Fallback help', 'Not recognised? Write to', 1],
            'gate.error_unknown' => ['gate', 'Error — address not on the list', "We can't find that address on our list — try the one the invitation came to.", 2],
            'gate.error_invalid' => ['gate', 'Error — not an email address', 'That does not look like an email address.', 2],

            // ---- The header ----------------------------------------------
            'header.greeting' => ['header', 'Greeting', 'Dear :name', 1],
            'header.greeting_fallback' => ['header', 'Greeting when we have no name', 'You are invited', 1],
            'header.seats_one' => ['header', 'Seats saved (one)', "We've saved a seat for you", 1],
            'header.seats_many' => ['header', 'Seats saved (several)', "We've saved :count seats for you", 1],
            'header.countdown_days' => ['header', 'Countdown', ':count days to go', 1],
            'header.countdown_tomorrow' => ['header', 'Countdown — the day before', 'tomorrow', 1],
            'header.countdown_today' => ['header', 'Countdown — the day itself', 'today', 1],

            // ---- Section headings ----------------------------------------
            'section.day' => ['sections', 'Schedule', 'The day', 1],
            'section.place' => ['sections', 'Venue', 'The place', 1],
            'section.travel' => ['sections', 'Travel', 'Getting to Granada', 1],
            'section.hotels' => ['sections', 'Hotels', 'Where to stay', 1],
            'section.highlights' => ['sections', 'Things to do', "While you're here", 1],
            'section.gallery' => ['sections', 'Photographs', 'Us, so far', 1],
            'section.questions' => ['sections', 'FAQs', 'Questions', 1],
            'link.maps' => ['sections', 'Maps link', 'Open in maps', 1],
            'link.book' => ['sections', 'Hotel booking link', 'Book', 1],

            // ---- The RSVP form -------------------------------------------
            'rsvp.deadline_prefix' => ['rsvp', 'Reply-by line', 'Please reply by :date', 1],
            'rsvp.heading' => ['rsvp', 'Heading', 'Will you be there?', 1],
            'rsvp.yes' => ['rsvp', 'Accept button', 'Yes, count us in', 1],
            'rsvp.no' => ['rsvp', 'Decline button', "Sadly we can't", 1],
            'rsvp.replied' => ['rsvp', 'Replied badge', 'Replied :date', 1],
            'rsvp.replied_undated' => ['rsvp', 'Replied badge (no date)', 'Replied', 1],
            'rsvp.confirm_attending' => ['rsvp', 'Confirmation — coming', "You're coming — wonderful.", 1],
            'rsvp.confirm_declined' => ['rsvp', 'Confirmation — not coming', "You can't make it.", 1],
            'rsvp.change' => ['rsvp', 'Change reply button', 'Change your reply', 1],
            'rsvp.party_heading' => ['rsvp', 'Party heading', "Who's coming", 1],
            'rsvp.party_seats_one' => ['rsvp', 'Party intro (one seat)', "We've saved a seat for you.", 1],
            'rsvp.party_seats_many' => ['rsvp', 'Party intro (several seats)', "We've saved :count seats for you.", 1],
            'rsvp.party_help' => ['rsvp', 'Party explainer', "Give us each name so we can write the place cards, and tell us about anything you can't eat.", 2],
            'rsvp.person_you' => ['rsvp', 'First person label', 'You', 1],
            'rsvp.person_other' => ['rsvp', 'Other people label', 'Guest :number', 1],
            'rsvp.remove' => ['rsvp', 'Remove person', 'Remove', 1],
            'rsvp.name_placeholder_you' => ['rsvp', 'Name box — you', 'Your name', 1],
            'rsvp.name_placeholder_other' => ['rsvp', 'Name box — someone else', 'Their full name', 1],
            'rsvp.dietary_placeholder' => ['rsvp', 'Dietary box', 'Dietary requirements — allergies, vegetarian, none', 1],
            'rsvp.add_one' => ['rsvp', 'Add person (one seat left)', 'Add someone — 1 seat left', 1],
            'rsvp.add_many' => ['rsvp', 'Add person (several seats left)', 'Add someone — :count seats left', 1],
            'rsvp.note_label' => ['rsvp', 'Notes label', 'Anything else?', 1],
            'rsvp.note_placeholder' => ['rsvp', 'Notes box', "Songs we have to play, who you'd like to sit with, when you're arriving — anything at all.", 2],
            'rsvp.submit' => ['rsvp', 'Send button', 'Send our reply', 1],
            'rsvp.submit_update' => ['rsvp', 'Send button when changing a reply', 'Update our reply', 1],
            'rsvp.submitting' => ['rsvp', 'Send button while sending', 'Sending…', 1],
            'rsvp.cancel' => ['rsvp', 'Cancel a change', 'Never mind, keep what we said', 1],
            'rsvp.email_prefix' => ['rsvp', 'Write-to-us line', "Rather write to us? We're at", 1],
            'rsvp.error_attending' => ['rsvp', 'Error — no answer given', 'Let us know whether you can make it.', 2],
            'rsvp.error_party' => ['rsvp', 'Error — nobody named', 'Tell us who is coming.', 2],
            'rsvp.error_party_max' => ['rsvp', 'Error — too many people', "We've only saved :count seats for you — send us a note if you need more.", 2],
            'rsvp.error_name' => ['rsvp', 'Error — a name is missing', 'We need a name for everyone coming.', 2],
            'rsvp.error_locked' => ['rsvp', 'Error — replying without unlocking', 'Open your invitation before replying.', 2],

            // ---- The footer ----------------------------------------------
            'footer.replay' => ['footer', 'Replay button', 'Open the letter again', 1],
        ];
    }

    /** Every default, as key => string. */
    public static function defaults(): array
    {
        return array_map(fn (array $row) => $row[2], static::definition());
    }

    /** Stored overrides merged over the defaults. */
    public static function all(): array
    {
        $stored = SiteText::query()
            ->pluck('value', 'key')
            ->filter(fn (?string $value) => filled($value))
            ->all();

        return array_merge(static::defaults(), array_intersect_key($stored, static::defaults()));
    }

    /** Only the wording the gate needs, so the rest stays behind the unlock. */
    public static function forGate(): array
    {
        return array_intersect_key(
            static::all(),
            array_filter(static::definition(), fn (array $row) => in_array($row[0], self::GATE_GROUPS, true))
        );
    }

    /** Everything the unlocked invitation needs. */
    public static function forInvitation(): array
    {
        return array_diff_key(static::all(), static::forGate());
    }

    /** One line, with `:name` placeholders filled in. */
    public static function line(string $key, array $replace = []): string
    {
        $value = static::all()[$key] ?? '';

        foreach ($replace as $token => $replacement) {
            $value = str_replace(':'.$token, (string) $replacement, $value);
        }

        return $value;
    }

    /** The definition grouped for the admin form, keyed by copy key. */
    public static function grouped(): Collection
    {
        // preserveKeys matters: the copy key becomes the form field's name.
        return collect(static::definition())->groupBy(fn (array $row) => $row[0], preserveKeys: true);
    }
}
