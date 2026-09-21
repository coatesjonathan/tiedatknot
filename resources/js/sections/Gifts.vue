<script setup>
import { useCopy } from '@/composables/useCopy'
import RevealSection from '@/components/RevealSection.vue'

defineProps({
    body: { type: String, default: '' },
    paymentMethods: { type: Array, default: () => [] },
})

const t = useCopy()
</script>

<template>
    <RevealSection class="mx-auto max-w-[55rem] px-6 pt-22 text-center">
        <p class="mb-5.5 text-xs font-medium uppercase tracking-[.3em] text-clay">{{ t('section.gifts') }}</p>

        <div
            class="rich text-[clamp(1.0625rem,1.6vw,1.3125rem)] leading-relaxed font-light text-ink [&_blockquote]:border-l-0 [&_blockquote]:pl-0 [&_ol]:list-inside [&_ol]:pl-0 [&_ul]:list-inside [&_ul]:pl-0"
            v-html="body"
        ></div>

        <div v-if="paymentMethods.length > 0" class="mt-12 flex flex-wrap justify-center gap-4">
            <a
                v-for="method in paymentMethods"
                :key="method.url"
                :href="method.url"
                target="_blank"
                rel="noopener"
                class="inline-flex items-center rounded-full border border-clay px-6 py-2 text-sm font-medium tracking-wide text-clay transition-colors hover:bg-clay hover:text-cream"
            >
                {{ method.label }}
            </a>
        </div>
    </RevealSection>
</template>
