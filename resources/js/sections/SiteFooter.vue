<script setup>
import { useCopy } from '@/composables/useCopy'

defineProps({ site: { type: Object, required: true } })

defineEmits(['replay'])

const t = useCopy()
</script>

<template>
    <footer class="flex flex-col items-center gap-[16px] bg-cream px-[24px] pb-[80px] pt-[66px]">
        <div
            class="flex size-[58px] items-center justify-center rounded-full bg-olive shadow-[inset_0_-4px_9px_rgba(23,33,21,.4)]"
        >
            <span class="font-serif text-[20px] text-[#f0ece8]">{{ site.monogram }}</span>
        </div>

        <p class="m-0 text-[13px] uppercase tracking-[.28em] text-ink-faint">
            {{ site.coupleNames }} · {{ site.locationLabel?.split(',')[0] }} · {{ site.weddingDateLabel }}
        </p>

        <button
            type="button"
            class="mt-[6px] cursor-pointer rounded-sheet border border-input-border px-[24px] py-[11px] text-[11px] font-medium uppercase tracking-[.26em] text-ink-faint"
            @click="$emit('replay')"
        >
            {{ t('footer.replay') }}
        </button>
    </footer>
</template>
