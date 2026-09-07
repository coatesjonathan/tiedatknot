<script setup>
defineProps({
    vals: { type: Object, required: true },
    monogram: { type: String, default: 'JJ' },
})

// The disc is drawn as three shards so the break reads as a real fracture.
const SHARD_TOP = 'M4,46 L18,40 L30,50 L44,38 L57,47 L72,35 L85,44 L96,38 C96,22 74,4 50,4 C26,4 4,22 4,46 Z'
const SHARD_LEFT = 'M4,46 L18,40 L30,50 L44,38 L48,60 L42,78 L48,96 C25,96 4,74 4,46 Z'
const SHARD_RIGHT = 'M44,38 L57,47 L72,35 L85,44 L96,38 C96,66 76,96 50,96 L42,78 L48,60 Z'
const DISC = 'M50,4 C74,4 96,22 96,50 C96,76 75,96 50,96 C25,96 4,76 4,50 C4,22 26,4 50,4 Z'
</script>

<template>
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

            <clipPath id="shardTClip"><path :d="SHARD_TOP" /></clipPath>
            <clipPath id="shardLClip"><path :d="SHARD_LEFT" /></clipPath>
            <clipPath id="shardRClip"><path :d="SHARD_RIGHT" /></clipPath>
        </defs>

        <g>
            <!-- Dark residue under the break, so the gaps read as wax rather than paper. -->
            <path
                :d="SHARD_LEFT"
                fill="#2c3a27"
                class="transition-opacity duration-[600ms] ease-[ease] [filter:url(#waxRough)]"
                :style="{ opacity: vals.residueOpacity }"
            />
            <path
                :d="SHARD_RIGHT"
                fill="#2c3a27"
                class="transition-opacity duration-[600ms] ease-[ease] [filter:url(#waxRough)]"
                :style="{ opacity: vals.residueOpacity }"
            />

            <path
                :d="DISC"
                fill="url(#waxG)"
                class="transition-opacity duration-[120ms] ease-[ease] [filter:url(#waxRough)]"
                :style="{ opacity: vals.waxBaseOpacity }"
            />

            <g
                class="[transform-box:fill-box] [transform-origin:50%_92%]"
                :style="{ transform: vals.shardT, opacity: vals.shardTOpacity, transition: vals.shardTrans }"
            >
                <path :d="SHARD_TOP" fill="url(#waxG)" class="[filter:url(#waxRough)]" />
                <g clip-path="url(#shardTClip)">
                    <circle cx="50" cy="50" r="50" fill="url(#waxSheen)" />
                    <ellipse cx="62" cy="78" rx="34" ry="24" fill="rgba(19,27,17,.2)" />
                    <circle cx="50" cy="50" r="39.5" fill="none" stroke="rgba(19,27,17,.26)" stroke-width="2.2" />
                    <circle cx="50" cy="50" r="37" fill="none" stroke="rgba(233,240,231,.2)" stroke-width="1.4" />
                    <text x="50" y="67.4" text-anchor="middle" class="font-serif text-[42px] tracking-[1px] fill-[rgba(19,27,17,.6)]">{{ monogram }}</text>
                    <text x="50" y="65.6" text-anchor="middle" class="font-serif text-[42px] tracking-[1px] fill-[rgba(225,235,223,.34)]">{{ monogram }}</text>
                    <circle cx="50" cy="50" r="50" fill="url(#waxRim)" />
                </g>
            </g>

            <g
                class="[transform-box:fill-box] [transform-origin:80%_10%]"
                :style="{ transform: vals.shardL, opacity: vals.shardLROpacity, transition: vals.shardTrans }"
            >
                <path :d="SHARD_LEFT" fill="url(#waxG)" class="[filter:url(#waxRough)]" />
                <g clip-path="url(#shardLClip)">
                    <circle cx="50" cy="50" r="50" fill="url(#waxSheen)" />
                    <ellipse cx="62" cy="78" rx="34" ry="24" fill="rgba(19,27,17,.2)" />
                    <circle cx="50" cy="50" r="39.5" fill="none" stroke="rgba(19,27,17,.26)" stroke-width="2.2" />
                    <circle cx="50" cy="50" r="37" fill="none" stroke="rgba(233,240,231,.2)" stroke-width="1.4" />
                    <text x="50" y="67.4" text-anchor="middle" class="font-serif text-[42px] tracking-[1px] fill-[rgba(19,27,17,.6)]">{{ monogram }}</text>
                    <text x="50" y="65.6" text-anchor="middle" class="font-serif text-[42px] tracking-[1px] fill-[rgba(225,235,223,.34)]">{{ monogram }}</text>
                    <circle cx="50" cy="50" r="50" fill="url(#waxRim)" />
                </g>
            </g>

            <g
                class="[transform-box:fill-box] [transform-origin:6%_8%]"
                :style="{ transform: vals.shardR, opacity: vals.shardLROpacity, transition: vals.shardTrans }"
            >
                <path :d="SHARD_RIGHT" fill="url(#waxG)" class="[filter:url(#waxRough)]" />
                <g clip-path="url(#shardRClip)">
                    <circle cx="50" cy="50" r="50" fill="url(#waxSheen)" />
                    <ellipse cx="62" cy="78" rx="34" ry="24" fill="rgba(19,27,17,.2)" />
                    <circle cx="50" cy="50" r="39.5" fill="none" stroke="rgba(19,27,17,.26)" stroke-width="2.2" />
                    <circle cx="50" cy="50" r="37" fill="none" stroke="rgba(233,240,231,.2)" stroke-width="1.4" />
                    <text x="50" y="67.4" text-anchor="middle" class="font-serif text-[42px] tracking-[1px] fill-[rgba(19,27,17,.6)]">{{ monogram }}</text>
                    <text x="50" y="65.6" text-anchor="middle" class="font-serif text-[42px] tracking-[1px] fill-[rgba(225,235,223,.34)]">{{ monogram }}</text>
                    <circle cx="50" cy="50" r="50" fill="url(#waxRim)" />
                </g>
            </g>
        </g>

        <!-- Hairline cracks draw across the wax before anything moves. -->
        <g class="pointer-events-none transition-opacity duration-[220ms] ease-[ease]" :style="{ opacity: vals.crackOpacity }">
            <path
                d="M4,46 L18,40 L30,50 L44,38 L57,47 L72,35 L85,44 L96,38"
                fill="none"
                stroke="rgba(14,20,13,.72)"
                stroke-width="1.5"
                stroke-linejoin="round"
                stroke-dasharray="120"
                class="[transition:stroke-dashoffset_320ms_linear]"
                :style="{ strokeDashoffset: vals.crackDash }"
            />
            <path
                d="M44,38 L48,60 L42,78 L48,96"
                fill="none"
                stroke="rgba(14,20,13,.55)"
                stroke-width="1.2"
                stroke-linejoin="round"
                stroke-dasharray="70"
                class="[transition:stroke-dashoffset_280ms_linear_130ms]"
                :style="{ strokeDashoffset: vals.crackDash2 }"
            />
        </g>
    </svg>
</template>
