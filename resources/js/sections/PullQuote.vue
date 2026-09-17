<script setup>
import RevealSection from '@/components/RevealSection.vue'

defineProps({ quote: { type: String, default: '' } })
</script>

<template>
    <RevealSection class="mx-auto max-w-180 px-6 pt-20 text-center">
        <!--
            Everything here is the couple's own formatting. `rich` carries the
            type scale — a Heading 1 becomes the big cursive line, the rest is
            body copy — so this wrapper only sets the block's own voice: centred,
            a size up from the page default, and quotes without their side rule.
        -->
        <div
            class="rich text-[clamp(17px,1.6vw,21px)] leading-[1.6] font-light text-ink [&_blockquote]:border-l-0 [&_blockquote]:pl-0 [&_ol]:list-inside [&_ol]:pl-0 [&_ul]:list-inside [&_ul]:pl-0"
            v-html="quote"
        ></div>
    </RevealSection>
</template>
