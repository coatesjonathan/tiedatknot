import { onBeforeUnmount } from 'vue'

/** Matches `scroll-margin-top` on anchored sections in app.css. */
export const SCROLL_OFFSET = 64

/**
 * Scrolls to an anchored section and keeps it there.
 *
 * Webfonts and images landing after the scroll has started reflow the page and
 * drag the section off target — sometimes more than once — so once the smooth
 * scroll has finished we pin the section to its offset until the layout stops
 * moving. Any scroll of the guest's own hands control straight back.
 */
export function useSectionScroll() {
    let frame
    let until = 0

    const release = () => {
        cancelAnimationFrame(frame)
        frame = undefined
        until = 0
    }

    const hold = (target) => {
        until = performance.now() + 1600

        const tick = () => {
            if (performance.now() > until) return release()

            const drift = target.getBoundingClientRect().top - SCROLL_OFFSET

            if (Math.abs(drift) > 2) window.scrollBy({ top: drift, behavior: 'auto' })

            frame = requestAnimationFrame(tick)
        }

        frame = requestAnimationFrame(tick)
    }

    const scrollToSection = (anchor) => {
        const target = document.getElementById(anchor)
        if (! target) return false

        release()

        const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches

        target.scrollIntoView({ behavior: reduced ? 'auto' : 'smooth', block: 'start' })

        // Let the smooth scroll finish before taking over.
        if (! reduced) setTimeout(() => hold(target), 650)

        return true
    }

    window.addEventListener('wheel', release, { passive: true })
    window.addEventListener('touchstart', release, { passive: true })

    onBeforeUnmount(() => {
        release()
        window.removeEventListener('wheel', release)
        window.removeEventListener('touchstart', release)
    })

    return { scrollToSection }
}
