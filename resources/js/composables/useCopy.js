import { inject, unref } from 'vue'

export const COPY_KEY = Symbol('copy')

/**
 * Admin-editable wording. `Invitation.vue` provides the map; every component
 * pulls what it needs by key, so a new line of copy needs no prop plumbing.
 *
 *   const t = useCopy()
 *   t('rsvp.heading')
 *   t('header.seats_many', { count: 3 })
 */
export function useCopy() {
    const copy = inject(COPY_KEY, {})

    return (key, replace = {}) => {
        let value = unref(copy)?.[key] ?? ''

        for (const [token, replacement] of Object.entries(replace)) {
            value = value.replaceAll(`:${token}`, replacement)
        }

        return value
    }
}
