<script setup>
import RevealSection from '@/components/RevealSection.vue'

defineProps({ quote: { type: String, default: '' } })
</script>

<template>
    <RevealSection class="mx-auto max-w-180 px-6 pt-20 text-center">
        <div
            class="rich m-0 [&>strong]:font-serif [&>strong]:text-[clamp(24px,3vw,34px)] leading-[1.4] text-ink text-pretty"
            v-html="quote"
        ></div>
    </RevealSection>
</template>
