<script setup>
defineProps({
    monogram: { type: String, default: 'JJ' },
})

const DISC = 'M50,4 C74,4 96,22 96,50 C96,76 75,96 50,96 C25,96 4,76 4,50 C4,22 26,4 50,4 Z'
</script>

<template>
    <!--
        One poured disc, drawn whole. It never breaks — the sequence presses it
        and fades it out as the flap peels away beneath it.
    -->
    <svg viewBox="0 0 100 100" class="block size-full overflow-visible">
        <defs>
            <radialGradient id="waxG" cx="33%" cy="23%" r="82%">
                <stop offset="0%" stop-color="#7f9c78" />
                <stop offset="38%" stop-color="#6d8a67" />
                <stop offset="84%" stop-color="#55704f" />
                <stop offset="100%" stop-color="#3d5138" />
            </radialGradient>

            <!-- Displaces every edge so the wax looks hand-poured, not cut. -->
            <filter id="waxRough" x="-25%" y="-25%" width="150%" height="150%">
                <feTurbulence type="fractalNoise" baseFrequency="0.06" numOctaves="3" seed="9" result="tn" />
                <feDisplacementMap in="SourceGraphic" in2="tn" scale="3.4" xChannelSelector="R" yChannelSelector="G" />
            </filter>

            <radialGradient id="waxRim" cx="50%" cy="50%" r="50%">
                <stop offset="62%" stop-color="rgba(19,27,17,0)" />
                <stop offset="88%" stop-color="rgba(19,27,17,.18)" />
                <stop offset="100%" stop-color="rgba(16,23,14,.45)" />
            </radialGradient>

            <linearGradient id="waxSheen" x1="18%" y1="6%" x2="72%" y2="86%">
                <stop offset="0%" stop-color="rgba(236,242,234,.42)" />
                <stop offset="42%" stop-color="rgba(236,242,234,.05)" />
                <stop offset="100%" stop-color="rgba(236,242,234,0)" />
            </linearGradient>

            <clipPath id="discClip"><path :d="DISC" /></clipPath>
        </defs>

        <path :d="DISC" fill="url(#waxG)" class="[filter:url(#waxRough)]" />

        <g clip-path="url(#discClip)">
            <circle cx="50" cy="50" r="50" fill="url(#waxSheen)" />
            <ellipse cx="62" cy="78" rx="34" ry="24" fill="rgba(19,27,17,.2)" />
            <circle cx="50" cy="50" r="39.5" fill="none" stroke="rgba(19,27,17,.26)" stroke-width="2.2" />
            <circle cx="50" cy="50" r="37" fill="none" stroke="rgba(233,240,231,.2)" stroke-width="1.4" />
            <text x="50" y="67.4" text-anchor="middle" class="font-serif text-[42px] tracking-[1px] fill-[rgba(19,27,17,.6)]">{{ monogram }}</text>
            <text x="50" y="65.6" text-anchor="middle" class="font-serif text-[42px] tracking-[1px] fill-[rgba(225,235,223,.34)]">{{ monogram }}</text>
            <circle cx="50" cy="50" r="50" fill="url(#waxRim)" />
        </g>
    </svg>
</template>
