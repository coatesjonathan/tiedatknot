<script setup>
import { useCopy } from '@/composables/useCopy'
import RevealSection from '@/components/RevealSection.vue'

defineProps({
    intro: { type: String, default: '' },
    options: { type: Array, default: () => [] },
    footnote: { type: String, default: '' },
})

const t = useCopy()
</script>

<template>
    <RevealSection class="mx-auto max-w-[70rem] px-6 pt-22">
        <p class="mb-2 text-xs font-medium uppercase tracking-[.3em] text-clay">{{ t('section.travel') }}</p>
        <div class="rich mb-7.5 max-w-[40rem] text-[1.0625rem] font-light leading-relaxed text-ink-soft" v-html="intro"></div>

        <!-- 2px gaps over the rule colour give hairline dividers between cards -->
        <div class="grid gap-[2px] bg-rule [grid-template-columns:repeat(auto-fit,minmax(17.5rem,1fr))]">
            <div v-for="option in options" :key="option.title" class="bg-card px-7 py-8">
                <p class="m-0 text-[0.6875rem] font-medium uppercase tracking-[.26em] text-olive">{{ option.label }}</p>
                <h3 class="mt-3 font-serif text-[1.875rem] font-normal text-ink">{{ option.title }}</h3>
                <div class="rich mt-3.5 text-base font-light leading-relaxed text-ink-soft" v-html="option.body"></div>
                <div
                    v-if="option.footnote"
                    class="rich mt-3.5 text-[0.9375rem] font-light text-ink-muted"
                    v-html="option.footnote"
                ></div>
            </div>
        </div>

        <div v-if="footnote" class="rich mt-4.5 text-[0.9375rem] font-light italic text-ink-ghost" v-html="footnote"></div>
    </RevealSection>
</template>
