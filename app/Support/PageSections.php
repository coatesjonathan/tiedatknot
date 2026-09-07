<?php

namespace App\Support;

/**
 * The sections the invitation page can render, in the order it renders them.
 *
 * These ids are structural: they are written onto the sections in
 * `resources/js/pages/Invitation.vue` and are what the menu scrolls to, so the
 * two lists have to agree. A test holds them to it.
 *
 * The couple's own choices for each — shown or not, in the menu or not, and
 * what the menu calls it — live on the SiteSection model.
 */
class PageSections
{
    /** anchor => the menu label it starts with. */
    public const ALL = [
        'the-day' => 'The day',
        'the-place' => 'The place',
        'getting-there' => 'Getting there',
        'where-to-stay' => 'Where to stay',
        'while-youre-here' => "While you're here",
        'gallery' => 'Photos',
        'good-to-know' => 'Good to know',
        'questions' => 'Questions',
        'rsvp' => 'RSVP',
    ];

    /** @return array<int, string> */
    public static function anchors(): array
    {
        return array_keys(self::ALL);
    }

    /** The rows the site starts life with: everything on, in page order. */
    public static function defaults(): array
    {
        $rows = [];

        foreach (array_values(self::anchors()) as $index => $anchor) {
            $rows[] = [
                'anchor' => $anchor,
                'label' => self::ALL[$anchor],
                'is_published' => true,
                'in_menu' => true,
                'sort_order' => $index,
            ];
        }

        return $rows;
    }
}
