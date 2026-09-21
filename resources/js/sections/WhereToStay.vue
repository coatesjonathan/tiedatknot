<script setup>
import { useCopy } from '@/composables/useCopy'
import { computed } from 'vue'
import RevealSection from '@/components/RevealSection.vue'

const props = defineProps({
    intro: { type: String, default: '' },
    blockCode: { type: String, default: '' },
    hotels: { type: Array, default: () => [] },
})

const ESCAPES = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }

// The intro carries a :code placeholder so the block code can be set in one place.
const introHtml = computed(() => {
    const code = String(props.blockCode ?? '').replace(/[&<>"']/g, (char) => ESCAPES[char])

    return (props.intro ?? '').replaceAll(':code', code ? `<strong>${code}</strong>` : '')
})

const t = useCopy()
</script>

<template>
    <RevealSection class="mx-auto max-w-280 px-6 pt-22">
        <p class="mb-2 text-xs font-medium uppercase tracking-[.3em] text-clay">{{ t('section.hotels') }}</p>

        <div
            class="rich mb-7.5 text-[1.0625rem] font-light leading-relaxed text-ink-soft"
            v-html="introHtml"
        ></div>

        <div class="grid gap-6.5 grid-cols-[repeat(auto-fit,minmax(18.75rem,1fr))]">
            <div v-for="hotel in hotels" :key="hotel.name + hotel.label" class="flex flex-col bg-card">
                <div class="h-45 overflow-hidden">
                    <img v-if="hotel.image" :src="hotel.image" :alt="hotel.name" loading="lazy" class="size-full object-cover" />
                </div>

                <div class="px-6 pb-7 pt-6.5">
                    <p class="m-0 text-[0.6875rem] font-medium uppercase tracking-[.26em] text-olive">{{ hotel.label }}</p>
                    <h3 class="mt-2.5 font-serif text-[1.75rem] font-normal text-ink">{{ hotel.name }}</h3>
                    <div class="rich mt-2.5 text-base font-light leading-[1.65] text-ink-soft" v-html="hotel.description"></div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <span v-if="hotel.rate" class="bg-sand px-2.75 py-1.5 text-xs tracking-[.06em] text-ink-soft">{{ hotel.rate }}</span>
                        <span v-if="hotel.roomsHeld" class="bg-sand px-2.75 py-1.5 text-xs tracking-[.06em] text-ink-soft">{{ hotel.roomsHeld }}</span>
                        <span v-if="hotel.releaseDate" class="bg-sand px-2.75 py-1.5 text-xs tracking-[.06em] text-ink-soft">{{ t('hotels.release', { date: hotel.releaseDate }) }}</span>
                    </div>

                    <p v-if="hotel.bookingUrl" class="mt-4">
                        <a
                            :href="hotel.bookingUrl"
                            target="_blank"
                            rel="noopener"
                            class="border-b border-olive/40 text-xs font-medium uppercase tracking-[.24em] text-olive hover:border-olive-dark hover:text-olive-dark"
                            >{{ t('link.book') }}</a
                        >
                    </p>
                </div>
            </div>
        </div>
    </RevealSection>
</template>
