<script setup>
defineProps({
    src: { type: String, default: null },
    alt: { type: String, default: '' },
})
</script>

<template>
    <div class="mx-auto mt-[54px] max-w-[1120px] px-[24px]">
        <div class="h-[clamp(300px,44vw,520px)] overflow-hidden rounded-sheet bg-card">
            <img v-if="src" :src="src" :alt="alt" class="size-full object-cover" />
        </div>
    </div>
</template>
