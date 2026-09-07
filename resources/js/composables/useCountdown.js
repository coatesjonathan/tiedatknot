import { computed, onBeforeUnmount, ref } from 'vue'

/** "N days to go", refreshed every minute so an open tab stays honest. */
export function useCountdown(site) {
    const now = ref(Date.now())
    const timer = setInterval(() => (now.value = Date.now()), 60_000)

    onBeforeUnmount(() => clearInterval(timer))

    return computed(() => {
        if (! site.countdownEnabled || ! site.weddingDate) return ''

        const target = new Date(`${site.weddingDate}T${site.weddingTime ?? '18:45'}:00+02:00`)
        const days = Math.ceil((target - now.value) / 86_400_000)

        if (days > 1) return `${days} days to go`
        if (days === 1) return 'tomorrow'
        if (days === 0) return 'today'

        return ''
    })
}
