<script setup>
import { computed } from 'vue'
import Envelope from './Envelope.vue'

const props = defineProps({
    vals: { type: Object, required: true },
    stage: { type: String, required: true },
    site: { type: Object, required: true },
    email: { type: String, default: '' },
    error: { type: String, default: '' },
    processing: { type: Boolean, default: false },
})

const emit = defineEmits(['update:email', 'submit'])

const idle = computed(() => props.stage === 'gate')
</script>

<template>
    <div
        class="fixed inset-0 z-40 flex flex-col items-center justify-center bg-sand transition-opacity duration-[600ms] ease-[ease]"
        :class="idle ? 'overflow-auto' : 'overflow-hidden'"
        :style="{ opacity: vals.gateOpacity }"
    >
        <!-- Soft vignette behind the envelope -->
        <div
            class="pointer-events-none absolute left-1/2 top-1/2 size-[min(660px,86vw)] -translate-x-1/2 -translate-y-1/2 rounded-full bg-cream"
        ></div>

        <p
            class="relative mb-[min(26px,3vh)] mt-[min(44px,4.5vh)] text-[12px] font-normal uppercase tracking-[.34em] text-clay"
        >
            {{ site.eyebrow }}
        </p>

        <Envelope :vals="vals" :site="site" :floating="idle" />

        <div
            class="relative mb-[min(46px,5vh)] mt-[min(40px,4.5vh)] w-[min(400px,84vw)] text-center transition-opacity duration-[350ms] ease-[ease]"
            :style="{ opacity: vals.formOpacity }"
            :inert="! idle"
        >
            <p class="mb-[18px] text-[16px] font-light leading-[1.6] text-ink-soft">
                Sealed for you. Enter the email your invitation came to.
            </p>

            <form class="flex flex-col gap-[12px]" @submit.prevent="emit('submit')">
                <input
                    type="email"
                    name="email"
                    autocomplete="email"
                    placeholder="you@example.com"
                    :value="email"
                    class="w-full rounded-sheet border border-input-border bg-paper px-[16px] py-[15px] text-center text-[16px] text-ink"
                    @input="emit('update:email', $event.target.value)"
                />
                <button
                    type="submit"
                    :disabled="processing"
                    class="cursor-pointer rounded-sheet bg-terracotta p-[15px] text-[12px] font-medium uppercase tracking-[.26em] text-paper disabled:opacity-70"
                >
                    Break the seal
                </button>
            </form>

            <p class="mt-[14px] min-h-[34px] text-[15px] font-light leading-[1.5] text-terracotta-dark">
                {{ error }}
            </p>

            <p class="text-[14px] font-light text-ink-faint">
                Not recognised? Write to
                <a
                    :href="`mailto:${site.contactEmail}`"
                    class="border-b border-terracotta/40 text-terracotta hover:border-terracotta-dark hover:text-terracotta-dark"
                    >{{ site.contactEmail }}</a
                >
            </p>
        </div>
    </div>
</template>
