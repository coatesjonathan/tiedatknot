<script setup>
import RevealSection from '@/components/RevealSection.vue'

defineProps({ notes: { type: Array, default: () => [] } })
</script>

<template>
    <RevealSection class="mt-22 bg-sand-deep px-6 py-[4.375rem]">
        <div class="mx-auto grid max-w-[85rem] gap-[2.125rem] [grid-template-columns:repeat(auto-fit,minmax(15.625rem,1fr))]">
            <div v-for="note in notes" :key="note.title">
                <h3 class="m-0 font-serif text-[1.625rem] font-normal text-ink">{{ note.title }}</h3>
                <div class="rich mt-2 text-base font-light leading-relaxed text-ink-soft" v-html="note.body"></div>
            </div>
        </div>
    </RevealSection>
</template>
