<script setup>
import RevealSection from '@/components/RevealSection.vue'

defineProps({ quote: { type: String, default: '' } })
</script>

<template>
    <RevealSection class="mx-auto max-w-[720px] px-[24px] pt-[80px] text-center">
        <p class="m-0 font-serif text-[clamp(24px,3vw,34px)] leading-[1.4] text-ink text-pretty">{{ quote }}</p>
    </RevealSection>
</template>
