<script setup>
import { vReveal } from '@/composables/useReveal'
</script>

<template>
    <section
        v-reveal
        class="translate-y-[18px] opacity-0 transition-[opacity,transform] duration-[800ms] ease-[ease] data-shown:translate-y-0 data-shown:opacity-100"
    >
        <slot />
    </section>
</template>
