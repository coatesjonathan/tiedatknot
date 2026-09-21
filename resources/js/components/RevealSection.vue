<script setup>
import { vReveal } from '@/composables/useReveal'
</script>

<template>
    <section
        v-reveal
        class="translate-y-4.5 opacity-0 transition-[opacity,transform] duration-[800ms] ease-linear data-shown:translate-y-0 data-shown:opacity-100"
    >
        <slot />
    </section>
</template>
