<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { useSectionScroll } from '@/composables/useSectionScroll'

const props = defineProps({
    items: { type: Array, default: () => [] },
})

// A link whose section isn't on the page (an empty gallery, say) would scroll
// nowhere, so the menu only shows the ones that resolve.
const live = ref([])
const active = ref('')
const open = ref(false)
const root = ref(null)

let observer

const activeLabel = computed(
    () => live.value.find((item) => item.anchor === active.value)?.label ?? '',
)

const { scrollToSection } = useSectionScroll()

const go = (anchor) => {
    open.value = false
    active.value = anchor
    scrollToSection(anchor)
}

// The panel is a mobile-only affordance: closing it on Escape, on a tap
// outside, and when the layout grows back to the full-width row.
const onKeydown = (event) => {
    if (event.key === 'Escape') open.value = false
}

const onPointerDown = (event) => {
    if (open.value && root.value && ! root.value.contains(event.target)) open.value = false
}

const onResize = () => {
    if (window.innerWidth >= 640) open.value = false
}

onMounted(() => {
    live.value = props.items.filter((item) => document.getElementById(item.anchor))

    // Mark whichever section is nearest the top of the viewport.
    observer = new IntersectionObserver(
        (entries) => {
            const visible = entries
                .filter((entry) => entry.isIntersecting)
                .sort((a, b) => a.boundingClientRect.top - b.boundingClientRect.top)[0]

            if (visible) active.value = visible.target.id
        },
        { rootMargin: '-72px 0px -55% 0px', threshold: 0 },
    )

    live.value.forEach((item) => {
        const el = document.getElementById(item.anchor)
        if (el) observer.observe(el)
    })

    window.addEventListener('keydown', onKeydown)
    window.addEventListener('pointerdown', onPointerDown)
    window.addEventListener('resize', onResize)
})

onBeforeUnmount(() => {
    observer?.disconnect()
    window.removeEventListener('keydown', onKeydown)
    window.removeEventListener('pointerdown', onPointerDown)
    window.removeEventListener('resize', onResize)
})
</script>

<template>
    <nav
        v-if="live.length"
        ref="root"
        class="sticky top-0 z-30 border-b border-rule/70"
        :class="open ? 'bg-sand' : 'bg-sand/90 backdrop-blur-[6px]'"
        aria-label="Sections"
    >
        <!-- Narrow screens: where you are, and a button for the rest. -->
        <div class="flex items-center justify-between gap-[12px] px-[18px] py-[11px] sm:hidden">
            <span class="truncate text-[11px] font-medium uppercase tracking-[.2em] text-ink-muted">
                {{ activeLabel }}
            </span>

            <button
                type="button"
                :aria-expanded="open"
                aria-controls="site-nav-links"
                class="-mr-[6px] flex cursor-pointer items-center gap-[8px] rounded-sheet border-0 bg-transparent p-[6px] text-[11px] font-medium uppercase tracking-[.2em] text-ink"
                @click="open = ! open"
            >
                {{ open ? 'Close' : 'Menu' }}
                <svg width="18" height="18" viewBox="0 0 18 18" aria-hidden="true" fill="none">
                    <template v-if="open">
                        <path d="M4 4 L14 14 M14 4 L4 14" stroke="currentColor" stroke-width="1.5" />
                    </template>
                    <template v-else>
                        <path d="M2 5h14M2 9h14M2 13h14" stroke="currentColor" stroke-width="1.5" />
                    </template>
                </svg>
            </button>
        </div>

        <ul
            id="site-nav-links"
            class="m-0 list-none border-rule/60 p-0 sm:flex sm:items-center sm:justify-center sm:gap-[6px] sm:border-t-0 sm:px-[16px] sm:py-[10px]"
            :class="
                open
                    ? 'block max-h-[70vh] overflow-y-auto border-t px-[10px] pb-[10px] pt-[6px] sm:max-h-none sm:overflow-visible'
                    : 'hidden'
            "
        >
            <li v-for="item in live" :key="item.anchor" class="sm:shrink-0">
                <a
                    :href="`#${item.anchor}`"
                    :aria-current="active === item.anchor ? 'true' : undefined"
                    class="block rounded-sheet px-[12px] py-[11px] text-[12px] font-medium uppercase tracking-[.2em] transition-colors duration-[200ms] sm:whitespace-nowrap sm:py-[7px] sm:text-[11px]"
                    :class="
                        active === item.anchor
                            ? 'bg-olive text-paper'
                            : 'text-ink-muted hover:bg-card hover:text-ink'
                    "
                    @click.prevent="go(item.anchor)"
                    >{{ item.label }}</a
                >
            </li>
        </ul>
    </nav>
</template>
