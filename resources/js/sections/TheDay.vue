<script setup>
import { useCopy } from '@/composables/useCopy'
import RevealSection from '@/components/RevealSection.vue'

defineProps({
    schedule: { type: Array, default: () => [] },
    footnote: { type: String, default: '' },
})

const t = useCopy()
</script>

<template>
    <RevealSection class="mx-auto max-w-[820px] px-[24px] pt-[88px]">
        <p class="mb-[26px] text-[12px] font-medium uppercase tracking-[.3em] text-clay">{{ t('section.day') }}</p>

        <div class="grid">
            <div
                v-for="(item, index) in schedule"
                :key="item.time + item.title"
                class="grid grid-cols-[96px_1fr] gap-[22px] border-t border-rule py-[20px]"
                :class="index === schedule.length - 1 && 'border-b'"
            >
                <p class="m-0 font-serif text-[22px] text-olive">{{ item.time }}</p>
                <div>
                    <p class="m-0 text-[19px] font-normal">{{ item.title }}</p>
                    <p v-if="item.detail" class="mt-[4px] text-[16px] font-light text-ink-muted">{{ item.detail }}</p>
                </div>
            </div>
        </div>

        <p v-if="footnote" class="mt-[14px] text-[14px] font-light italic text-ink-ghost">{{ footnote }}</p>
    </RevealSection>
</template>
