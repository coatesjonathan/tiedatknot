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
    /**
     * Block elements the editor can align, and which may therefore carry a
     * `style` attribute through the sanitiser.
     *
     * @var list<string>
     */
    private const ALIGNABLE = ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote', 'li', 'td', 'th'];

    /**
     * CSS declarations kept on a surviving `style` attribute. Everything the
     * editor writes is typography; anything else is dropped.
     *
     * @var list<string>
     */
    private const ALLOWED_STYLES = ['text-align', 'color', 'background-color'];

    /**
     * Classes the editor applies through its own tools (`lead`, text colours).
     *
     * @var list<string>
     */
    private const ALLOWED_CLASSES = ['lead'];

    private static ?HtmlSanitizer $sanitizer = null;

    /** Sanitised HTML, ready to render. */
    public static function html(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        $clean = trim(static::sanitizer()->sanitize($value));
        $clean = static::filterStyles($clean);
        $clean = static::filterClasses($clean);

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

    /**
     * Keep only typography declarations on `style` attributes. The sanitiser
     * passes the attribute through verbatim, so the CSS is vetted here.
     */
    private static function filterStyles(string $html): string
    {
        return preg_replace_callback(
            '/ style="([^"]*)"/i',
            function (array $match): string {
                $kept = [];

                foreach (explode(';', html_entity_decode($match[1])) as $declaration) {
                    if (! str_contains($declaration, ':')) {
                        continue;
                    }

                    [$property, $rawValue] = explode(':', $declaration, 2);

                    $property = strtolower(trim($property));
                    $value = trim($rawValue);

                    if (! in_array($property, static::ALLOWED_STYLES, true)) {
                        continue;
                    }

                    // Literal values only: a keyword, a hex colour or a number.
                    // Parentheses are refused outright, which rules out `url()`
                    // and the legacy `expression()` alike.
                    if (! preg_match('/^[a-z0-9#%.,\s-]+$/i', $value)) {
                        continue;
                    }

                    $kept[] = "{$property}: {$value}";
                }

                return $kept === [] ? '' : ' style="'.e(implode('; ', $kept)).'"';
            },
            $html
        ) ?? $html;
    }

    /** Drop any class the editor's own tools didn't put there. */
    private static function filterClasses(string $html): string
    {
        return preg_replace_callback(
            '/ class="([^"]*)"/i',
            function (array $match): string {
                $kept = array_values(array_intersect(
                    preg_split('/\s+/', trim(html_entity_decode($match[1]))) ?: [],
                    static::ALLOWED_CLASSES
                ));

                return $kept === [] ? '' : ' class="'.e(implode(' ', $kept)).'"';
            },
            $html
        ) ?? $html;
    }

    private static function sanitizer(): HtmlSanitizer
    {
        if (static::$sanitizer !== null) {
            return static::$sanitizer;
        }

        $config = (new HtmlSanitizerConfig)
            // Structure and headings — `h1` is the script flourish on the page.
            ->allowElement('p')
            ->allowElement('br')
            ->allowElement('hr')
            ->allowElement('h1')
            ->allowElement('h2')
            ->allowElement('h3')
            ->allowElement('h4')
            ->allowElement('h5')
            ->allowElement('h6')
            ->allowElement('blockquote')
            ->allowElement('div')
            // Inline marks the editor's toolbar can apply.
            ->allowElement('strong')
            ->allowElement('b')
            ->allowElement('em')
            ->allowElement('i')
            ->allowElement('u')
            ->allowElement('s')
            ->allowElement('mark')
            ->allowElement('small')
            ->allowElement('sub')
            ->allowElement('sup')
            ->allowElement('span')
            ->allowElement('code')
            ->allowElement('pre')
            // Lists.
            ->allowElement('ul')
            ->allowElement('ol')
            ->allowElement('li')
            // Tables.
            ->allowElement('table')
            ->allowElement('thead')
            ->allowElement('tbody')
            ->allowElement('tfoot')
            ->allowElement('tr')
            ->allowElement('th', ['colspan', 'rowspan'])
            ->allowElement('td', ['colspan', 'rowspan'])
            // Collapsible sections.
            ->allowElement('details')
            ->allowElement('summary')
            ->allowElement('a', ['href', 'target', 'rel'])
            ->allowLinkSchemes(['https', 'http', 'mailto', 'tel'])
            ->forceAttribute('a', 'rel', 'noopener noreferrer')
            ->forceAttribute('a', 'target', '_blank')
            ->allowAttribute('class', ['p', 'span', 'div'])
            ->allowAttribute('style', [...static::ALIGNABLE, 'span']);

        return static::$sanitizer = new HtmlSanitizer($config);
    }
}
