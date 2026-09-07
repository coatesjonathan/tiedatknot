const ESCAPES = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }

const escape = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ESCAPES[char])

/**
 * Admin-authored copy is plain text, but the couple can drop in a markdown link
 * — [renfe.com](https://renfe.com) — and a blank line for a paragraph break.
 * Everything is escaped first, so only the anchors we build survive.
 */
export function richText(value) {
    return escape(value)
        .replace(
            /\[([^\]]+)\]\((https?:\/\/[^\s)]+|mailto:[^\s)]+)\)/g,
            '<a href="$2" target="_blank" rel="noopener" class="border-b border-olive/40 text-olive hover:border-olive-dark hover:text-olive-dark">$1</a>',
        )
        .replace(/\n/g, '<br>')
}
