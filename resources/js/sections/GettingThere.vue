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
    <RevealSection class="mx-auto max-w-[1120px] px-[24px] pt-[88px]">
        <p class="mb-[8px] text-[12px] font-medium uppercase tracking-[.3em] text-clay">{{ t('section.travel') }}</p>
        <div class="rich mb-[30px] max-w-[640px] text-[17px] font-light leading-[1.7] text-ink-soft" v-html="intro"></div>

        <!-- 2px gaps over the rule colour give hairline dividers between cards -->
        <div class="grid gap-[2px] bg-rule [grid-template-columns:repeat(auto-fit,minmax(280px,1fr))]">
            <div v-for="option in options" :key="option.title" class="bg-card px-[28px] py-[32px]">
                <p class="m-0 text-[11px] font-medium uppercase tracking-[.26em] text-olive">{{ option.label }}</p>
                <h3 class="mt-[12px] font-serif text-[30px] font-normal text-ink">{{ option.title }}</h3>
                <div class="rich mt-[14px] text-[16px] font-light leading-[1.7] text-ink-soft" v-html="option.body"></div>
                <div
                    v-if="option.footnote"
                    class="rich mt-[14px] text-[15px] font-light text-ink-muted"
                    v-html="option.footnote"
                ></div>
            </div>
        </div>

        <div v-if="footnote" class="rich mt-[18px] text-[15px] font-light italic text-ink-ghost" v-html="footnote"></div>
    </RevealSection>
</template>
