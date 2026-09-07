<script setup>
defineProps({ site: { type: Object, required: true } })

defineEmits(['replay'])
</script>

<template>
    <footer class="flex flex-col items-center gap-[16px] bg-cream px-[24px] pb-[80px] pt-[66px]">
        <div
            class="flex size-[58px] items-center justify-center rounded-full bg-terracotta shadow-[inset_0_-4px_9px_rgba(94,32,15,.4)]"
        >
            <span class="font-serif text-[20px] text-[#f7ddc6]">{{ site.monogram }}</span>
        </div>

        <p class="m-0 text-[13px] uppercase tracking-[.28em] text-ink-faint">
            {{ site.coupleNames }} · {{ site.locationLabel?.split(',')[0] }} · {{ site.weddingDateLabel }}
        </p>

        <button
            type="button"
            class="mt-[6px] cursor-pointer rounded-sheet border border-input-border px-[24px] py-[11px] text-[11px] font-medium uppercase tracking-[.26em] text-ink-faint"
            @click="$emit('replay')"
        >
            Open the letter again
        </button>
    </footer>
</template>
