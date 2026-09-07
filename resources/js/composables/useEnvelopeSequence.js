import { computed, onBeforeUnmount, ref } from 'vue'

/** The stages, in the order they run. */
const ORDER = ['gate', 'crack', 'split', 'peel', 'swing', 'rise', 'draw', 'unfold', 'lift', 'fade', 'open']

/** Every delay is multiplied by the speed factor for the chosen tempo. */
const SPEEDS = { quick: 0.75, full: 1, cinematic: 1.35 }

/** Timeline in milliseconds at ×1.0, exactly as specced in the handoff. */
const TIMELINE = [
    [480, 'split'],
    [880, 'peel'],
    [1240, 'swing'],
    [1900, 'rise'],
    [2600, 'draw'],
    [3500, 'unfold'],
    [4400, 'lift'],
    [4860, 'fade'],
    [5460, 'open'],
]

const prefersReducedMotion = () =>
    typeof window !== 'undefined' && window.matchMedia('(prefers-reduced-motion: reduce)').matches

/**
 * The envelope state machine. `stage` advances on timers; `vals` is a single
 * lookup table from stage to the transform values every layer reads, so the
 * animation stays declarative and there is one place to tune it.
 */
export function useEnvelopeSequence(speed = 'quick', startOpen = false) {
    const stage = ref(startOpen ? 'open' : 'gate')
    let timeouts = []

    const clear = () => {
        timeouts.forEach(clearTimeout)
        timeouts = []
    }

    const run = (onComplete) => {
        clear()

        // Reduced motion: no envelope theatre, just a short cross-fade.
        if (prefersReducedMotion()) {
            stage.value = 'fade'
            timeouts.push(
                setTimeout(() => {
                    stage.value = 'open'
                    onComplete?.()
                }, 600),
            )

            return
        }

        const factor = SPEEDS[speed] ?? SPEEDS.quick

        stage.value = 'crack'

        TIMELINE.forEach(([ms, next]) => {
            timeouts.push(
                setTimeout(() => {
                    stage.value = next
                    if (next === 'open') onComplete?.()
                }, ms * factor),
            )
        })
    }

    const reset = () => {
        clear()
        stage.value = 'gate'
    }

    onBeforeUnmount(clear)

    const past = (name) => ORDER.indexOf(stage.value) >= ORDER.indexOf(name)

    const vals = computed(() => {
        const cracked = past('split')

        return {
            gateOn: stage.value !== 'open',
            gateOverflow: stage.value === 'gate' ? 'auto' : 'hidden',
            contentOn: past('fade'),
            gateOpacity: past('fade') ? 0 : 1,
            contentOpacity: past('fade') ? 1 : 0,
            formOpacity: stage.value === 'gate' ? 1 : 0,

            envTilt: past('lift') ? 0 : past('rise') ? 6 : 13,
            envY: past('lift') ? '120px' : past('unfold') ? '70px' : past('rise') ? '26px' : '0px',
            envScale: past('lift') ? 1.03 : 1,
            envOpacity: past('fade') ? 0 : past('lift') ? 0.32 : 1,
            groundScale: past('lift') ? 1.12 : 1,
            groundOpacity: past('fade') ? 0 : past('rise') ? 0.75 : 1,

            flapDeg: past('swing') ? -179 : past('peel') ? -15 : 0,
            flapShadow: past('rise') ? 0 : past('peel') ? 1 : 0,

            // The letter sits at z-2, behind the pocket front (z-3), so the part
            // still inside the envelope is genuinely hidden by the paper in
            // front of it. It only clears to the top once it is fully drawn out.
            letterZ: past('unfold') ? 9 : 2,
            // The envelope is a preserve-3d scene, so paint order follows real
            // depth, not z-index. Park the letter behind the pocket front's
            // plane while it is inside, and bring it forward once it is out.
            letterDepth: past('unfold') ? 18 : -10,
            // -60% leaves the card straddling the mouth — half out, half still
            // in the pocket — before -88% draws it fully clear to unfold.
            letterY: past('lift')
                ? '-80%'
                : past('unfold')
                  ? '-84%'
                  : past('draw')
                    ? '-88%'
                    : past('rise')
                      ? '-60%'
                      : '0%',
            letterTilt: past('lift') ? 0 : past('unfold') ? -3 : past('rise') ? -7 : 0,
            letterStretch: past('unfold') ? 1 : past('rise') ? 1.02 : 1,
            letterScale: past('lift') ? 1.06 : 1,
            letterShadow: past('rise')
                ? '0 24px 38px -22px rgba(39,51,37,.5)'
                : '0 2px 5px -3px rgba(39,51,37,.35)',
            // The folded panels rotate out of the letter's plane, so while the
            // card is still in the pocket they would poke through the envelope
            // front in 3D. They only join once the letter has cleared the mouth.
            panelOpacity: past('draw') ? 1 : 0,
            foldTop: past('unfold') ? 0 : 91,
            foldBot: past('unfold') ? 0 : -92,

            // Once the flap is flat back, it drops below the letter so the
            // paper draws over it as it comes up out of the mouth.
            flapZ: past('rise') ? 1 : 6,

            mouthShadow: past('lift') ? 0 : past('rise') ? 1 : 0,
            lipOpacity: past('lift') ? 0 : past('peel') ? 1 : 0,

            // The seal presses square on the flap — the monogram reads upright.
            // It swells a touch while the cracks run, then gives.
            sealBot: cracked ? 'translateZ(4px) scale(1)' : past('crack') ? 'translateZ(4px) scale(1.035)' : 'translateZ(4px) scale(1)',
            sealBotOpacity: past('lift') ? 0 : 1,

            // The top piece rides up and away with the flap; the two lower
            // pieces break free, then drop off the envelope under gravity.
            shardT: past('peel')
                ? 'translate(-17px,-38px) rotate(-34deg) scale(.94)'
                : cracked
                  ? 'translate(-3px,-9px) rotate(-7deg)'
                  : 'none',
            shardTOpacity: past('swing') ? 0 : 1,
            shardL: past('swing')
                ? 'translate(-27px,74px) rotate(-56deg) scale(.92)'
                : cracked
                  ? 'translate(-9px,6px) rotate(-14deg)'
                  : 'none',
            shardR: past('swing')
                ? 'translate(29px,80px) rotate(62deg) scale(.92)'
                : cracked
                  ? 'translate(9px,8px) rotate(12deg)'
                  : 'none',
            shardLROpacity: past('rise') ? 0 : 1,

            // Crisp snap on the break, then a gravity curve as they fall away.
            shardTrans: past('swing')
                ? 'transform 820ms cubic-bezier(.42,0,.86,.44), opacity 620ms ease-in 200ms'
                : 'transform 560ms cubic-bezier(.2,.92,.28,1), opacity 400ms ease',

            waxBaseOpacity: cracked ? 0 : 1,
            // Wax left clinging to the paper: it shows in the gaps as the pieces
            // part, then goes with them rather than sitting there as a blot.
            residueOpacity: past('swing') ? 0 : cracked ? 0.58 : 0,
            // The hairlines only exist while the wax is still whole — once it
            // gives, the shard edges are the break.
            crackOpacity: cracked ? 0 : past('crack') ? 1 : 0,
            crackDash: past('crack') ? 0 : 120,
            crackDash2: past('crack') ? 0 : 70,

            chipsOpacity: cracked ? 0 : 1,
            chipA: cracked ? 'translateZ(6px) translate(-41px,63px) rotate(-190deg)' : 'translateZ(6px)',
            chipB: cracked ? 'translateZ(6px) translate(37px,69px) rotate(165deg)' : 'translateZ(6px)',
            chipC: cracked ? 'translateZ(6px) translate(-14px,77px) rotate(-110deg)' : 'translateZ(6px)',
            chipD: cracked ? 'translateZ(6px) translate(23px,55px) rotate(128deg)' : 'translateZ(6px)',
        }
    })

    return { stage, vals, run, reset }
}
