import { computed, onBeforeUnmount, ref } from 'vue'

/** The nine stages, in the order they run. */
const ORDER = ['gate', 'crack', 'split', 'peel', 'swing', 'rise', 'unfold', 'lift', 'fade', 'open']

/** Every delay is multiplied by the speed factor for the chosen tempo. */
const SPEEDS = { quick: 0.75, full: 1, cinematic: 1.35 }

/** Timeline in milliseconds at ×1.0, exactly as specced in the handoff. */
const TIMELINE = [
    [340, 'split'],
    [760, 'peel'],
    [1120, 'swing'],
    [1820, 'rise'],
    [3060, 'unfold'],
    [3960, 'lift'],
    [4420, 'fade'],
    [5020, 'open'],
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

            letterZ: past('rise') ? 9 : 2,
            letterY: past('lift') ? '-40%' : past('unfold') ? '-38%' : past('rise') ? '-52%' : '0%',
            letterTilt: past('lift') ? 0 : past('unfold') ? -3 : past('rise') ? -9 : 0,
            letterStretch: past('unfold') ? 1 : past('rise') ? 1.03 : 1,
            letterScale: past('lift') ? 1.06 : 1,
            letterShadow: past('rise')
                ? '0 24px 38px -22px rgba(80,45,25,.5)'
                : '0 2px 5px -3px rgba(80,45,25,.35)',
            panelOpacity: past('rise') ? 1 : 0,
            foldTop: past('unfold') ? 0 : 91,
            foldBot: past('unfold') ? 0 : -92,

            mouthShadow: past('lift') ? 0 : past('rise') ? 1 : 0,
            lipOpacity: past('lift') ? 0 : past('peel') ? 1 : 0,

            sealBot: 'translateZ(4px) rotate(-11deg)',
            sealBotOpacity: past('lift') ? 0 : 1,
            shardT: past('peel')
                ? 'translate(-13px,-26px) rotate(-22deg)'
                : cracked
                  ? 'translate(-2px,-6px) rotate(-4deg)'
                  : 'none',
            shardTOpacity: past('swing') ? 0 : 1,
            shardL: cracked ? 'translate(-7px,5px) rotate(-11deg)' : 'none',
            shardR: cracked ? 'translate(7px,7px) rotate(9deg)' : 'none',
            waxBaseOpacity: cracked ? 0 : 1,
            crackOpacity: past('crack') ? 1 : 0,
            crackDash: past('crack') ? 0 : 120,
            crackDash2: past('crack') ? 0 : 70,

            chipsOpacity: cracked ? 0 : 1,
            chipA: cracked ? 'translateZ(6px) translate(-34px,52px) rotate(-140deg)' : 'translateZ(6px)',
            chipB: cracked ? 'translateZ(6px) translate(30px,58px) rotate(120deg)' : 'translateZ(6px)',
            chipC: cracked ? 'translateZ(6px) translate(-12px,64px) rotate(-80deg)' : 'translateZ(6px)',
            chipD: cracked ? 'translateZ(6px) translate(18px,46px) rotate(96deg)' : 'translateZ(6px)',
        }
    })

    return { stage, vals, run, reset }
}
