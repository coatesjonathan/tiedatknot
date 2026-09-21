<script setup>
import { useCopy } from '@/composables/useCopy'
import RevealSection from '@/components/RevealSection.vue'

defineProps({ highlights: { type: Array, default: () => [] } })

const t = useCopy()
</script>

<template>
    <RevealSection class="mx-auto max-w-[85rem] px-6 pt-22">
        <p class="mb-[1.625rem] text-xs font-medium uppercase tracking-[.3em] text-clay">{{ t('section.highlights') }}</p>

        <div class="grid gap-[2.125rem] [grid-template-columns:repeat(auto-fit,minmax(14.375rem,1fr))]">
            <div v-for="item in highlights" :key="item.title">
                <h3 class="m-0 font-serif text-[1.625rem] font-normal text-ink">{{ item.title }}</h3>
                <div class="rich mt-2 text-base font-light leading-relaxed text-ink-soft" v-html="item.body"></div>
            </div>
        </div>
    </RevealSection>
</template>
