<script setup>
import { useCountdown } from '@/composables/useCountdown'

const props = defineProps({
    site: { type: Object, required: true },
    guest: { type: Object, default: null },
})

const countdown = useCountdown(props.site)
</script>

<template>
    <header class="flex flex-col items-center px-[24px] pt-[86px] text-center">
        <p class="m-0 text-[12px] font-normal uppercase tracking-[.32em] text-clay">
            {{ guest?.greeting ?? 'You are invited' }}
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
    </header>
</template>
