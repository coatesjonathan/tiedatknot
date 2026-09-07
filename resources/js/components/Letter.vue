<script setup>
defineProps({
    vals: { type: Object, required: true },
    site: { type: Object, required: true },
})
</script>

<template>
    <!--
        Three stacked panels folded inside the pocket. The outer two are rotated
        flat at `unfold`; all three stay invisible until the letter has cleared
        the envelope mouth so nothing shows through the paper.
    -->
    <div
        class="absolute inset-x-[5%] top-[11%] transform-3d [transform-origin:50%_100%] transition-transform duration-[1250ms] ease-[cubic-bezier(.2,.76,.2,1)]"
        :style="{
            zIndex: vals.letterZ,
            transform: `translateY(${vals.letterY}) rotateX(${vals.letterTilt}deg) scale(${vals.letterScale}) scaleY(${vals.letterStretch})`,
        }"
    >
        <div
            class="relative flex h-[84px] flex-col items-center justify-center gap-[9px] overflow-hidden transform-3d bg-[linear-gradient(180deg,#fffdf7_0%,#fdf7ec_88%,#f0e3cf_100%)] [transform-origin:bottom_center] transition-transform duration-[900ms] ease-[cubic-bezier(.3,.8,.25,1)]"
            :style="{
                transform: `rotateX(${vals.foldTop}deg)`,
                opacity: vals.panelOpacity,
                boxShadow: vals.letterShadow,
            }"
        >
            <div class="grain-soft"></div>
            <p class="m-0 font-serif text-[26px] text-terracotta">
                {{ site.firstName.charAt(0) }} &amp; {{ site.secondName.charAt(0) }}
            </p>
            <p class="m-0 text-[10px] uppercase tracking-[.34em] text-clay">
                {{ site.locationLabel?.split(',')[0] }}
            </p>
        </div>

        <div
            class="relative flex h-[92px] flex-col items-center justify-center gap-[7px] overflow-hidden bg-[linear-gradient(180deg,#f6ecdb_0%,#fffdf7_12%,#fffdf7_88%,#f4e8d5_100%)]"
            :style="{ boxShadow: vals.letterShadow }"
        >
            <div class="grain-soft"></div>
            <p class="m-0 font-serif text-[30px] leading-[1.1] text-ink">
                {{ site.firstName }} <span class="italic text-terracotta">&amp; {{ site.secondName }}</span>
            </p>
            <p class="m-0 text-[10px] uppercase tracking-[.32em] text-ink-faint">{{ site.weddingDateLabel }}</p>
        </div>

        <div
            class="relative flex h-[84px] items-center justify-center overflow-hidden transform-3d bg-[linear-gradient(180deg,#f0e3cf_0%,#fdf7ec_14%,#fffdf7_100%)] [transform-origin:top_center] transition-transform duration-[900ms] ease-[cubic-bezier(.3,.8,.25,1)]"
            :style="{
                transform: `rotateX(${vals.foldBot}deg)`,
                opacity: vals.panelOpacity,
                boxShadow: vals.letterShadow,
            }"
        >
            <div class="grain-soft"></div>
            <p class="m-0 text-[11px] uppercase tracking-[.3em] text-clay">{{ site.venueName }}</p>
        </div>
    </div>
</template>
