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
    <RevealSection class="mx-auto max-w-[51.25rem] px-6 pt-22">
        <p class="mb-[1.625rem] text-xs font-medium uppercase tracking-[.3em] text-clay">{{ t('section.day') }}</p>

        <div class="grid">
            <div
                v-for="(item, index) in schedule"
                :key="item.time + item.title"
                class="grid grid-cols-[6rem_1fr] gap-5.5 border-t border-rule py-5"
                :class="index === schedule.length - 1 && 'border-b'"
            >
                <p class="m-0 font-serif text-[1.375rem] text-olive">{{ item.time }}</p>
                <div>
                    <p class="m-0 text-[1.1875rem] font-normal">{{ item.title }}</p>
                    <p v-if="item.detail" class="mt-1 text-base font-light text-ink-muted">{{ item.detail }}</p>
                </div>
            </div>
        </div>

        <div v-if="footnote" class="rich mt-3.5 text-sm font-light italic text-ink-ghost" v-html="footnote"></div>
    </RevealSection>
</template>
