<script setup>
import { computed } from 'vue'
import { useCopy } from '@/composables/useCopy'
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

const t = useCopy()
</script>

<template>
    <div
        class="fixed inset-0 z-40 flex flex-col items-center justify-center bg-sand transition-opacity duration-[600ms] ease-linear"
        :class="idle ? 'overflow-auto' : 'overflow-hidden'"
        :style="{ opacity: vals.gateOpacity }"
    >
        <!-- Soft vignette behind the envelope -->
        <div
            class="pointer-events-none absolute left-1/2 top-1/2 size-[min(41.25rem,86vw)] -translate-x-1/2 -translate-y-1/2 rounded-full bg-card"
        ></div>

        <p
            class="relative mb-[min(1.625rem,3vh)] mt-[min(2.75rem,4.5vh)] text-xs font-normal uppercase tracking-[.34em] text-clay"
        >
            {{ site.eyebrow }}
        </p>

        <Envelope :vals="vals" :site="site" :floating="idle" />

        <div
            class="relative mb-[min(2.875rem,5vh)] mt-[min(2.5rem,4.5vh)] w-[min(25rem,84vw)] text-center transition-opacity duration-[350ms] ease-linear"
            :style="{ opacity: vals.formOpacity }"
            :inert="! idle"
        >
            <p class="mb-4.5 text-base font-light leading-relaxed text-ink-soft">
                {{ t('gate.intro') }}
            </p>

            <form class="flex flex-col gap-3" @submit.prevent="emit('submit')">
                <input
                    type="email"
                    name="email"
                    autocomplete="email"
                    :placeholder="t('gate.email_placeholder')"
                    :value="email"
                    class="w-full rounded-sheet border border-input-border bg-paper px-4 py-[0.9375rem] text-center text-base text-ink"
                    @input="emit('update:email', $event.target.value)"
                />
                <button
                    type="submit"
                    :disabled="processing"
                    class="cursor-pointer rounded-sheet bg-olive p-[0.9375rem] text-xs font-medium uppercase tracking-[.26em] text-paper disabled:opacity-70"
                >
                    {{ t('gate.submit') }}
                </button>
            </form>

            <p class="mt-3.5 min-h-[2.125rem] text-[0.9375rem] font-light leading-normal text-olive-dark">
                {{ error }}
            </p>

            <p class="text-sm font-light text-ink-faint">
                {{ t('gate.help_prefix') }}
                <a
                    :href="`mailto:${site.contactEmail}`"
                    class="border-b border-olive/40 text-olive hover:border-olive-dark hover:text-olive-dark"
                    >{{ site.contactEmail }}</a
                >
            </p>
        </div>
    </div>
</template>
