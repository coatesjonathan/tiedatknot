<script setup>
import { useCopy } from '@/composables/useCopy'
import RevealSection from '@/components/RevealSection.vue'

defineProps({ highlights: { type: Array, default: () => [] } })

const t = useCopy()
</script>

<template>
    <RevealSection class="mx-auto max-w-[1120px] px-[24px] pt-[88px]">
        <p class="mb-[26px] text-[12px] font-medium uppercase tracking-[.3em] text-clay">{{ t('section.highlights') }}</p>

        <div class="grid gap-[34px] [grid-template-columns:repeat(auto-fit,minmax(230px,1fr))]">
            <div v-for="item in highlights" :key="item.title">
                <h3 class="m-0 font-serif text-[26px] font-normal text-ink">{{ item.title }}</h3>
                <div class="rich mt-[8px] text-[16px] font-light leading-[1.7] text-ink-soft" v-html="item.body"></div>
            </div>
        </div>
    </RevealSection>
</template>
