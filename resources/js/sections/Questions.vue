<script setup>
import { useCopy } from '@/composables/useCopy'
import RevealSection from '@/components/RevealSection.vue'

defineProps({ faqs: { type: Array, default: () => [] } })

const t = useCopy()
</script>

<template>
    <RevealSection class="mx-auto max-w-[820px] px-[24px] pt-[88px]">
        <p class="mb-[22px] text-[12px] font-medium uppercase tracking-[.3em] text-clay">{{ t('section.questions') }}</p>

        <details
            v-for="(faq, index) in faqs"
            :key="faq.question"
            class="group border-t border-rule"
            :class="index === faqs.length - 1 && 'border-b'"
        >
            <summary class="flex items-center justify-between gap-[16px] py-[20px] text-[19px] text-ink">
                {{ faq.question }}
                <span
                    class="font-serif text-[22px] text-olive transition-transform duration-[250ms] ease-[ease] group-open:rotate-45"
                    >+</span
                >
            </summary>
            <div class="rich mb-[22px] text-[16px] font-light leading-[1.7] text-ink-soft" v-html="faq.answer"></div>
        </details>
    </RevealSection>
</template>
