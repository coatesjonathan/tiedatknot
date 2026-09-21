<script setup>
import { useCopy } from '@/composables/useCopy'
import RevealSection from '@/components/RevealSection.vue'

defineProps({ faqs: { type: Array, default: () => [] } })

const t = useCopy()
</script>

<template>
    <RevealSection class="mx-auto max-w-[51.25rem] px-6 pt-22">
        <p class="mb-5.5 text-xs font-medium uppercase tracking-[.3em] text-clay">{{ t('section.questions') }}</p>

        <details
            v-for="(faq, index) in faqs"
            :key="faq.question"
            class="group border-t border-rule"
            :class="index === faqs.length - 1 && 'border-b'"
        >
            <summary class="flex items-center justify-between gap-4 py-5 text-[1.1875rem] text-ink">
                {{ faq.question }}
                <span
                    class="font-serif text-[1.375rem] text-olive transition-transform duration-[250ms] ease-linear group-open:rotate-45"
                    >+</span
                >
            </summary>
            <div class="rich mb-5.5 text-base font-light leading-relaxed text-ink-soft" v-html="faq.answer"></div>
        </details>
    </RevealSection>
</template>
