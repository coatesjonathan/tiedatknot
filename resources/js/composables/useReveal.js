/**
 * v-reveal — fades a section up as it comes into view. Sections start hidden and
 * transition once their top passes 90% of the viewport; reduced motion shows
 * them immediately (handled in CSS).
 */
export const vReveal = {
    mounted(el) {
        el.setAttribute('data-reveal', '')

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            el.setAttribute('data-shown', '')

            return
        }

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (! entry.isIntersecting) return

                    el.setAttribute('data-shown', '')
                    observer.unobserve(el)
                })
            },
            { rootMargin: '0px 0px -10% 0px' },
        )

        observer.observe(el)
        el._revealObserver = observer
    },

    unmounted(el) {
        el._revealObserver?.disconnect()
    },
}
