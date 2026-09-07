<script setup>
import { useCopy } from '@/composables/useCopy'
import RevealSection from '@/components/RevealSection.vue'

defineProps({ venue: { type: Object, required: true } })

const t = useCopy()
</script>

<template>
    <RevealSection class="mx-auto max-w-[1120px] px-[24px] pt-[88px]">
        <p class="mb-[26px] text-[12px] font-medium uppercase tracking-[.3em] text-clay">{{ t('section.place') }}</p>

        <div class="grid items-start gap-[40px] [grid-template-columns:repeat(auto-fit,minmax(290px,1fr))]">
            <div>
                <h2 class="m-0 font-serif text-[clamp(30px,4vw,44px)] font-normal leading-[1.1] text-ink">
                    {{ venue.name }}
                </h2>

                <div
                    class="rich mt-[18px] text-[17px] font-light leading-[1.7] text-ink-soft text-pretty"
                    v-html="venue.description"
                ></div>

                <p class="mt-[20px] whitespace-pre-line text-[16px] font-light leading-[1.7] text-ink-muted">
                    {{ venue.address }}
                </p>

                <div
                    v-if="venue.travelNote"
                    class="rich mt-[16px] text-[16px] font-light leading-[1.7] text-ink-muted"
                    v-html="venue.travelNote"
                ></div>

                <p v-if="venue.mapsUrl" class="mt-[22px]">
                    <a
                        :href="venue.mapsUrl"
                        target="_blank"
                        rel="noopener"
                        class="border-b border-olive/40 text-[12px] font-medium uppercase tracking-[.26em] text-olive hover:border-olive-dark hover:text-olive-dark"
                        >{{ t('link.maps') }}</a
                    >
                </p>
            </div>

            <div class="h-[clamp(260px,34vw,400px)] overflow-hidden rounded-sheet bg-card">
                <img v-if="venue.image" :src="venue.image" :alt="venue.name" loading="lazy" class="size-full object-cover" />
            </div>
        </div>
    </RevealSection>
</template>
