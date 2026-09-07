<?php

namespace App\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * Formatted copy written in the admin's rich editor.
 *
 * The editor is behind a login, but its output is rendered with `v-html`, so it
 * is sanitised down to the handful of tags the invitation's typography can
 * carry before it ever reaches a guest.
 */
class RichText
{
    private static ?HtmlSanitizer $sanitizer = null;

    /** Sanitised HTML, ready to render. */
    public static function html(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        $clean = trim(static::sanitizer()->sanitize($value));

        // An editor emptied by hand leaves empty tags behind — <p></p>,
        // <p><br /></p> and the like. Nothing readable means nothing to render.
        return blank(trim(strip_tags($clean))) ? null : $clean;
    }

    /**
     * Turn copy written as plain text into the HTML the editor now stores:
     * markdown links become anchors, blank lines become paragraphs and single
     * newlines become breaks — matching how the site used to render it.
     */
    public static function fromPlainText(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        $paragraphs = preg_split('/\R{2,}/', trim($value));

        $html = array_map(function (string $paragraph) {
            $escaped = e(trim($paragraph));

            $linked = preg_replace(
                '/\[([^\]]+)\]\((https?:\/\/[^\s)]+|mailto:[^\s)]+)\)/',
                '<a href="$2" target="_blank" rel="noopener">$1</a>',
                $escaped
            );

            return '<p>'.preg_replace('/\R/', '<br>', $linked).'</p>';
        }, $paragraphs);

        return implode('', $html);
    }

    /** Back to plain text, for rolling the migration back. */
    public static function toPlainText(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        $text = preg_replace('/<br\s*\/?>/i', "\n", $value);
        $text = preg_replace('/<\/p>\s*<p[^>]*>/i', "\n\n", $text);

        return trim(html_entity_decode(strip_tags($text)));
    }

    private static function sanitizer(): HtmlSanitizer
    {
        return static::$sanitizer ??= new HtmlSanitizer(
            (new HtmlSanitizerConfig)
                ->allowElement('p')
                ->allowElement('br')
                ->allowElement('strong')
                ->allowElement('em')
                ->allowElement('u')
                ->allowElement('s')
                ->allowElement('ul')
                ->allowElement('ol')
                ->allowElement('li')
                ->allowElement('a', ['href', 'target', 'rel'])
                ->allowLinkSchemes(['https', 'http', 'mailto', 'tel'])
                ->forceAttribute('a', 'rel', 'noopener noreferrer')
                ->forceAttribute('a', 'target', '_blank')
        );
    }
}
