<script setup>
import { useCopy } from '@/composables/useCopy'
import RevealSection from '@/components/RevealSection.vue'

defineProps({ images: { type: Array, default: () => [] } })

const t = useCopy()
</script>

<template>
    <RevealSection v-if="images.length" class="mx-auto max-w-[85rem] px-6 pt-22">
        <p class="mb-[1.625rem] text-xs font-medium uppercase tracking-[.3em] text-clay">{{ t('section.gallery') }}</p>

        <div class="columns-2 gap-3.5 md:columns-3 lg:columns-4">
            <div v-for="(image, index) in images" :key="index" class="mb-3.5 break-inside-avoid overflow-hidden bg-card">
                <img :src="image.image" :alt="image.caption ?? ''" loading="lazy" class="block h-auto w-full" />
            </div>
        </div>
    </RevealSection>
</template>
