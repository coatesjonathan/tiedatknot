<script setup>
import { computed } from 'vue'
import RevealSection from '@/components/RevealSection.vue'

const props = defineProps({ rsvp: { type: Object, required: true } })

const mailto = computed(() => `mailto:${props.rsvp.email}?subject=${encodeURIComponent(props.rsvp.subject)}`)
</script>

<template>
    <RevealSection class="mt-[90px] bg-terracotta px-[24px] pb-[96px] pt-[88px] text-center">
        <p v-if="rsvp.deadlineLabel" class="m-0 text-[12px] font-medium uppercase tracking-[.3em] text-paper/70">
            Please reply by {{ rsvp.deadlineLabel }}
        </p>

        <h2 class="mt-[20px] font-serif text-[clamp(38px,6vw,68px)] font-normal leading-[1.05] text-paper">
            Just write to us
        </h2>

        <p class="mx-auto mt-[18px] max-w-[520px] text-[17px] font-light leading-[1.7] text-paper/85">
            {{ rsvp.body }}
        </p>

        <p class="mt-[30px]">
            <a
                :href="mailto"
                class="inline-block max-w-full rounded-sheet bg-paper px-[34px] py-[16px] text-[13px] font-medium uppercase tracking-[.26em] text-terracotta [overflow-wrap:anywhere]"
                >{{ rsvp.email }}</a
            >
        </p>
    </RevealSection>
</template>
