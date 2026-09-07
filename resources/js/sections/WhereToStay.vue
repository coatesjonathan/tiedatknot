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
    <RevealSection class="mx-auto max-w-[1120px] px-[24px] pt-[88px]">
        <p class="mb-[8px] text-[12px] font-medium uppercase tracking-[.3em] text-clay">{{ t('section.hotels') }}</p>

        <div
            class="rich mb-[30px] max-w-[640px] text-[17px] font-light leading-[1.7] text-ink-soft"
            v-html="introHtml"
        ></div>

        <div class="grid gap-[26px] [grid-template-columns:repeat(auto-fit,minmax(300px,1fr))]">
            <div v-for="hotel in hotels" :key="hotel.name + hotel.label" class="flex flex-col bg-card">
                <div class="h-[180px] overflow-hidden">
                    <img v-if="hotel.image" :src="hotel.image" :alt="hotel.name" loading="lazy" class="size-full object-cover" />
                </div>

                <div class="px-[24px] pb-[28px] pt-[26px]">
                    <p class="m-0 text-[11px] font-medium uppercase tracking-[.26em] text-olive">{{ hotel.label }}</p>
                    <h3 class="mt-[10px] font-serif text-[28px] font-normal text-ink">{{ hotel.name }}</h3>
                    <div class="rich mt-[10px] text-[16px] font-light leading-[1.65] text-ink-soft" v-html="hotel.description"></div>

                    <div class="mt-[16px] flex flex-wrap gap-[8px]">
                        <span v-if="hotel.rate" class="bg-sand px-[11px] py-[6px] text-[12px] tracking-[.06em] text-ink-soft">{{ hotel.rate }}</span>
                        <span v-if="hotel.roomsHeld" class="bg-sand px-[11px] py-[6px] text-[12px] tracking-[.06em] text-ink-soft">{{ hotel.roomsHeld }}</span>
                        <span v-if="hotel.releaseDate" class="bg-sand px-[11px] py-[6px] text-[12px] tracking-[.06em] text-ink-soft">{{ t('hotels.release', { date: hotel.releaseDate }) }}</span>
                    </div>

                    <p v-if="hotel.bookingUrl" class="mt-[16px]">
                        <a
                            :href="hotel.bookingUrl"
                            target="_blank"
                            rel="noopener"
                            class="border-b border-olive/40 text-[12px] font-medium uppercase tracking-[.24em] text-olive hover:border-olive-dark hover:text-olive-dark"
                            >{{ t('link.book') }}</a
                        >
                    </p>
                </div>
            </div>
        </div>
    </RevealSection>
</template>
