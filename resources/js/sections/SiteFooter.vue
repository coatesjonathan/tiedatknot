<script setup>
import { useCopy } from '@/composables/useCopy'

defineProps({ site: { type: Object, required: true } })

defineEmits(['replay'])

const t = useCopy()
</script>

<template>
    <footer class="flex flex-col items-center gap-4 bg-cream px-6 pb-20 pt-[4.125rem]">
        <div
            class="flex size-[3.625rem] items-center justify-center rounded-full bg-olive shadow-[inset_0_-4px_9px_rgba(23,33,21,.4)]"
        >
            <span class="font-serif text-xl text-[#f0ece8]">{{ site.monogram }}</span>
        </div>

        <p class="m-0 text-[0.8125rem] uppercase tracking-[.28em] text-ink-faint">
            {{ site.coupleNames }} · {{ site.locationLabel?.split(',')[0] }} · {{ site.weddingDateLabel }}
        </p>

        <button
            type="button"
            class="mt-1.5 cursor-pointer rounded-sheet border border-input-border px-6 py-[0.6875rem] text-[0.6875rem] font-medium uppercase tracking-[.26em] text-ink-faint"
            @click="$emit('replay')"
        >
            {{ t('footer.replay') }}
        </button>
    </footer>
</template>
