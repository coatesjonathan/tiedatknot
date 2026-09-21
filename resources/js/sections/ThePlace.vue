<script setup>
import { useCopy } from '@/composables/useCopy'
import RevealSection from '@/components/RevealSection.vue'

defineProps({ venue: { type: Object, required: true } })

const t = useCopy()
</script>

<template>
    <RevealSection class="mx-auto max-w-[70rem] px-6 pt-22">
        <p class="mb-[1.625rem] text-xs font-medium uppercase tracking-[.3em] text-clay">{{ t('section.place') }}</p>

        <div class="grid items-start gap-10 [grid-template-columns:repeat(auto-fit,minmax(18.125rem,1fr))]">
            <div>
                <h2 class="m-0 font-serif text-[clamp(1.875rem,4vw,2.75rem)] font-normal leading-tight text-ink">
                    {{ venue.name }}
                </h2>

                <div
                    class="rich mt-4.5 text-[1.0625rem] font-light leading-relaxed text-ink-soft text-pretty"
                    v-html="venue.description"
                ></div>

                <p class="mt-5 whitespace-pre-line text-base font-light leading-relaxed text-ink-muted">
                    {{ venue.address }}
                </p>

                <div
                    v-if="venue.travelNote"
                    class="rich mt-4 text-base font-light leading-relaxed text-ink-muted"
                    v-html="venue.travelNote"
                ></div>

                <p v-if="venue.mapsUrl" class="mt-5.5">
                    <a
                        :href="venue.mapsUrl"
                        target="_blank"
                        rel="noopener"
                        class="border-b border-olive/40 text-xs font-medium uppercase tracking-[.26em] text-olive hover:border-olive-dark hover:text-olive-dark"
                        >{{ t('link.maps') }}</a
                    >
                </p>
            </div>

            <div class="h-[clamp(16.25rem,34vw,25rem)] overflow-hidden rounded-sheet bg-card">
                <img v-if="venue.image" :src="venue.image" :alt="venue.name" loading="lazy" class="size-full object-cover" />
            </div>
        </div>
    </RevealSection>
</template>
