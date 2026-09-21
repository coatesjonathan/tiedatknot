<script setup>
defineProps({
    src: { type: String, default: null },
    alt: { type: String, default: '' },
})
</script>

<template>
    <div class="mx-auto mt-[3.375rem] max-w-[70rem] px-6">
        <div class="h-[clamp(18.75rem,44vw,32.5rem)] overflow-hidden rounded-sheet bg-card">
            <img v-if="src" :src="src" :alt="alt" class="size-full object-cover" />
        </div>
    </div>
</template>
