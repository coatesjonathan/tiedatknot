<script setup>
import Letter from './Letter.vue'
import WaxSeal from './WaxSeal.vue'

defineProps({
    vals: { type: Object, required: true },
    site: { type: Object, required: true },
    floating: { type: Boolean, default: true },
})
</script>

<template>
    <div
        class="relative aspect-[14/9] w-[min(470px,84vw,68vh)] animate-float perspective-[1500px] [perspective-origin:50%_34%]"
        :style="{ animationPlayState: floating ? 'running' : 'paused' }"
    >
        <!-- Ground shadow -->
        <div
            class="absolute inset-x-[8%] -bottom-[26px] h-[30px] rounded-[50%] bg-[radial-gradient(ellipse_at_center,rgba(47,61,45,.34)_0%,rgba(47,61,45,0)_72%)] blur-[3px] [transition:transform_900ms_ease,opacity_900ms_ease]"
            :style="{ transform: `scaleX(${vals.groundScale})`, opacity: vals.groundOpacity }"
        ></div>

        <div
            class="absolute inset-0 transform-3d [transition:transform_1000ms_cubic-bezier(.32,.72,.22,1),opacity_800ms_ease]"
            :style="{
                transform: `rotateX(${vals.envTilt}deg) rotateZ(-.7deg) translateY(${vals.envY}) scale(${vals.envScale})`,
                opacity: vals.envOpacity,
            }"
        >
            <!-- 1. Back panel -->
            <div
                class="absolute inset-0 rounded-envelope bg-[linear-gradient(168deg,#faf8f6_0%,#f4f1ee_100%)] shadow-[0_30px_50px_-30px_rgba(39,51,37,.45)]"
            ></div>

            <!-- 2. Letter -->
            <Letter :vals="vals" :site="site" />

            <!-- 11. Slot shadow at the mouth, over the emerging paper -->
            <div
                class="pointer-events-none absolute inset-x-0 top-0 z-11 h-[34px] bg-[linear-gradient(180deg,rgba(43,55,41,.34)_0%,rgba(43,55,41,.12)_45%,rgba(43,55,41,0)_100%)] transition-opacity duration-[600ms] ease-[ease]"
                :style="{ opacity: vals.mouthShadow }"
            ></div>

            <!-- 12. Envelope lip, drawn over the letter so the paper slides out from behind it -->
            <div
                class="pointer-events-none absolute inset-x-0 top-0 z-12 h-[7px] rounded-t-envelope bg-[linear-gradient(180deg,#f6f4f2_0%,#edeae5_62%,#dedad3_100%)] shadow-[0_2px_3px_-1px_rgba(45,57,43,.28)] transition-opacity duration-[500ms] ease-[ease]"
                :style="{ opacity: vals.lipOpacity }"
            ></div>

            <!-- 3. Pocket front -->
            <div
                class="absolute inset-0 z-3 overflow-hidden rounded-envelope bg-[linear-gradient(172deg,#f6f4f1_0%,#ece9e4_100%)] shadow-[inset_0_1px_0_rgba(255,255,255,.6),inset_0_0_0_1px_rgba(106,126,102,.16)]"
            >
                <div class="grain"></div>
            </div>

            <!-- 4. Seams — the folded flaps behind the pocket -->
            <div
                class="absolute inset-0 z-4 rounded-envelope bg-[linear-gradient(100deg,rgba(255,255,255,.5)_0%,rgba(110,130,106,.09)_100%)] [clip-path:polygon(0_0,50%_62%,0_100%)]"
            ></div>
            <div
                class="absolute inset-0 z-4 rounded-envelope bg-[linear-gradient(260deg,rgba(255,255,255,.5)_0%,rgba(110,130,106,.09)_100%)] [clip-path:polygon(100%_0,50%_62%,100%_100%)]"
            ></div>
            <div
                class="absolute inset-0 z-4 rounded-envelope bg-[linear-gradient(0deg,rgba(110,130,106,.13)_0%,rgba(110,130,106,.02)_100%)] [clip-path:polygon(0_100%,50%_62%,100%_100%)]"
            ></div>

            <!-- 5. The flap's cast shadow on the body -->
            <div
                class="pointer-events-none absolute inset-0 z-5 bg-[linear-gradient(180deg,rgba(56,72,52,.30)_0%,rgba(56,72,52,.06)_62%,rgba(56,72,52,0)_100%)] transition-opacity duration-[700ms] ease-[ease] [clip-path:polygon(0_0,100%_0,50%_66%)]"
                :style="{ opacity: vals.flapShadow }"
            ></div>

            <!-- 6. Flap — two backface-hidden faces, so the lined underside shows past 90° -->
            <div
                class="absolute inset-x-0 top-0 h-[62%] transform-3d [transform-origin:top_center] transition-transform duration-[900ms] ease-[cubic-bezier(.36,.06,.2,1)]"
                :style="{ transform: `rotateX(${vals.flapDeg}deg)`, zIndex: vals.flapZ }"
            >
                <div
                    class="absolute inset-0 backface-hidden bg-[linear-gradient(186deg,#f0ece8_0%,#eae6e1_72%,#e4e0da_100%)] shadow-[0_8px_14px_-10px_rgba(39,51,37,.5)] [clip-path:polygon(0_0,100%_0,50%_100%)]"
                >
                    <div class="grain"></div>
                </div>
                <div
                    class="absolute inset-0 rotate-y-180 backface-hidden bg-[linear-gradient(186deg,#fdfcfb_0%,#f7f5f2_100%)] shadow-[inset_0_0_0_1px_rgba(140,160,136,.18)] [clip-path:polygon(0_0,100%_0,50%_100%)]"
                >
                    <div class="grain"></div>
                </div>
            </div>

            <!-- 7. Wax seal, pressed on the flap tip -->
            <div
                class="absolute left-1/2 top-[62%] z-7 -ml-[53px] -mt-[53px] size-[106px] drop-shadow-[0_4px_5px_rgba(27,39,25,.42)] [transition:transform_340ms_cubic-bezier(.34,1.32,.5,1),opacity_700ms_ease]"
                :style="{ transform: vals.sealBot, opacity: vals.sealBotOpacity }"
            >
                <WaxSeal :vals="vals" :monogram="site.monogram" />
            </div>

            <!-- Wax chips that scatter as the seal gives -->
            <div class="pointer-events-none absolute left-1/2 top-[62%] z-8 size-0">
                <div
                    class="absolute -ml-[22px] -mt-[4px] h-[7px] w-[9px] rounded-[44%_56%_48%_52%] bg-[#6d8a67] [transition:transform_1100ms_cubic-bezier(.3,.1,.85,.55),opacity_1100ms_ease]"
                    :style="{ transform: vals.chipA, opacity: vals.chipsOpacity }"
                ></div>
                <div
                    class="absolute ml-[14px] -mt-[2px] h-[6px] w-[7px] rounded-[52%_48%_44%_56%] bg-olive-dark [transition:transform_1100ms_cubic-bezier(.3,.1,.85,.55),opacity_1100ms_ease]"
                    :style="{ transform: vals.chipB, opacity: vals.chipsOpacity }"
                ></div>
                <div
                    class="absolute -ml-[8px] mt-[2px] size-[5px] rounded-full bg-olive [transition:transform_1100ms_cubic-bezier(.3,.1,.85,.55),opacity_1100ms_ease]"
                    :style="{ transform: vals.chipC, opacity: vals.chipsOpacity }"
                ></div>
                <div
                    class="absolute ml-[4px] h-[4px] w-[6px] rounded-[48%_52%_52%_48%] bg-[#7f9c78] [transition:transform_1100ms_cubic-bezier(.3,.1,.85,.55),opacity_1100ms_ease]"
                    :style="{ transform: vals.chipD, opacity: vals.chipsOpacity }"
                ></div>
            </div>
        </div>
    </div>
</template>
