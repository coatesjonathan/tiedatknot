<script setup>
import { useCopy } from '@/composables/useCopy'
import RevealSection from '@/components/RevealSection.vue'

defineProps({ images: { type: Array, default: () => [] } })

const t = useCopy()
</script>

<template>
    <RevealSection v-if="images.length" class="mx-auto max-w-[85rem] px-6 pt-22">
        <p class="mb-[1.625rem] text-xs font-medium uppercase tracking-[.3em] text-clay">{{ t('section.gallery') }}</p>

        <div class="grid gap-3.5 [grid-template-columns:repeat(auto-fit,minmax(12.5rem,1fr))]">
            <div
                v-for="(image, index) in images"
                :key="index"
                class="h-[18.75rem] overflow-hidden bg-card"
                :class="image.isWide && 'col-span-2'"
            >
                <img :src="image.image" :alt="image.caption ?? ''" loading="lazy" class="size-full object-cover" />
            </div>
        </div>
    </RevealSection>
</template>
