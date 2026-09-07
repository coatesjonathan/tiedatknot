<script setup>
import RevealSection from '@/components/RevealSection.vue'
import { richText } from '@/utils/richText'

defineProps({ notes: { type: Array, default: () => [] } })
</script>

<template>
    <RevealSection class="mt-[88px] bg-sand-deep px-[24px] py-[70px]">
        <div class="mx-auto grid max-w-[1120px] gap-[34px] [grid-template-columns:repeat(auto-fit,minmax(250px,1fr))]">
            <div v-for="note in notes" :key="note.title">
                <h3 class="m-0 font-serif text-[26px] font-normal text-ink">{{ note.title }}</h3>
                <p class="mt-[8px] text-[16px] font-light leading-[1.7] text-ink-soft" v-html="richText(note.body)"></p>
            </div>
        </div>
    </RevealSection>
</template>
