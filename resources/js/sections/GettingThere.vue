<script setup>
import RevealSection from '@/components/RevealSection.vue'
import { richText } from '@/utils/richText'

defineProps({
    intro: { type: String, default: '' },
    options: { type: Array, default: () => [] },
    footnote: { type: String, default: '' },
})
</script>

<template>
    <RevealSection class="mx-auto max-w-[1120px] px-[24px] pt-[88px]">
        <p class="mb-[8px] text-[12px] font-medium uppercase tracking-[.3em] text-clay">Getting to Granada</p>
        <p class="mb-[30px] max-w-[640px] text-[17px] font-light leading-[1.7] text-ink-soft">{{ intro }}</p>

        <!-- 2px gaps over the rule colour give hairline dividers between cards -->
        <div class="grid gap-[2px] bg-rule [grid-template-columns:repeat(auto-fit,minmax(280px,1fr))]">
            <div v-for="option in options" :key="option.title" class="bg-card px-[28px] py-[32px]">
                <p class="m-0 text-[11px] font-medium uppercase tracking-[.26em] text-terracotta">{{ option.label }}</p>
                <h3 class="mt-[12px] font-serif text-[30px] font-normal text-ink">{{ option.title }}</h3>
                <p class="mt-[14px] text-[16px] font-light leading-[1.7] text-ink-soft">{{ option.body }}</p>
                <p
                    v-if="option.footnote"
                    class="mt-[14px] text-[15px] font-light text-ink-muted"
                    v-html="richText(option.footnote)"
                ></p>
            </div>
        </div>

        <p v-if="footnote" class="mt-[18px] text-[15px] font-light italic text-ink-ghost">{{ footnote }}</p>
    </RevealSection>
</template>
