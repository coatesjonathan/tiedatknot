<script setup>
import { computed } from 'vue'
import { useCopy } from '@/composables/useCopy'
import { useCountdown } from '@/composables/useCountdown'
import { useSectionScroll } from '@/composables/useSectionScroll'

const props = defineProps({
    site: { type: Object, required: true },
    guest: { type: Object, default: null },
    rsvp: { type: Object, default: null },
    showRsvpLink: { type: Boolean, default: true },
})

const t = useCopy()
const countdown = useCountdown(props.site, t)

const { scrollToSection } = useSectionScroll()

// A guest who has already answered is offered a change, not a fresh reply.
const replied = computed(() => props.rsvp && props.rsvp.status !== 'pending')

const ctaLabel = computed(() => (replied.value ? t('header.rsvp_cta_replied') : t('header.rsvp_cta')))
</script>

<template>
    <header class="flex flex-col items-center px-[24px] pt-[86px] text-center">
        <p class="m-0 text-[12px] font-normal uppercase tracking-[.32em] text-clay">
            {{ guest?.greeting ?? t('header.greeting_fallback') }}
        </p>

        <h1
            class="mt-[22px] font-serif text-[clamp(56px,11vw,130px)] font-normal leading-[.95] tracking-[-.01em] text-ink"
        >
            {{ site.firstName }}<span class="block italic text-olive">&amp; {{ site.secondName }}</span>
        </h1>

        <p class="mt-[30px] text-[15px] uppercase tracking-[.28em] text-ink-soft">
            {{ site.weddingDateLabel }} · {{ site.locationLabel }}
        </p>

        <p class="mt-[12px] text-[16px] font-light text-ink-faint">
            {{ site.venueName }}<template v-if="countdown"> · {{ countdown }}</template>
        </p>

        <p v-if="guest?.seatLine" class="mt-[6px] text-[16px] font-light text-olive">{{ guest.seatLine }}</p>

        <!-- The main thing we want from a guest, put where they land. -->
        <a
            v-if="showRsvpLink"
            href="#rsvp"
            class="mt-[30px] inline-flex items-center gap-[12px] rounded-sheet bg-olive px-[42px] py-[20px] text-[14px] font-medium uppercase tracking-[.24em] text-paper shadow-[0_10px_24px_-14px_rgba(58,70,54,.85)] transition-colors duration-[200ms] hover:bg-olive-dark"
            @click.prevent="scrollToSection('rsvp')"
        >
            {{ ctaLabel }}
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M8 2v11M3.5 8.5 8 13l4.5-4.5" stroke="currentColor" stroke-width="1.6" />
            </svg>
        </a>
    </header>
</template>
