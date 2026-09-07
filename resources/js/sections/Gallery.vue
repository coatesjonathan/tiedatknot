<script setup>
import RevealSection from '@/components/RevealSection.vue'

defineProps({ images: { type: Array, default: () => [] } })
</script>

<template>
    <RevealSection v-if="images.length" class="mx-auto max-w-[1120px] px-[24px] pt-[88px]">
        <p class="mb-[26px] text-[12px] font-medium uppercase tracking-[.3em] text-clay">Us, so far</p>

        <div class="grid gap-[14px] [grid-template-columns:repeat(auto-fit,minmax(200px,1fr))]">
            <div
                v-for="(image, index) in images"
                :key="index"
                class="h-[300px] overflow-hidden bg-card"
                :class="image.isWide && 'col-span-2'"
            >
                <img :src="image.image" :alt="image.caption ?? ''" loading="lazy" class="size-full object-cover" />
            </div>
        </div>
    </RevealSection>
</template>
