import { computed, onBeforeUnmount, ref } from 'vue'

/** The stages, in the order they run. */
const ORDER = ['gate', 'release', 'peel', 'rise', 'unfold', 'fade', 'open']

/** Every delay is multiplied by the speed factor for the chosen tempo. */
const SPEEDS = { quick: 0.75, full: 1, cinematic: 1.35 }

/**
 * Timeline in milliseconds at ×1.0 — a little under three seconds end to end.
 *
 * Four beats carry it: the seal gives, the flap opens, the card draws out and
 * unfolds, then the page takes over. `release` is set when the run starts.
 */
const TIMELINE = [
    [420, 'peel'],
    [1080, 'rise'],
    [1720, 'unfold'],
    [2380, 'fade'],
    [2900, 'open'],
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

        stage.value = 'release'

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

    const vals = computed(() => ({
        gateOn: stage.value !== 'open',
        contentOn: past('fade'),
        gateOpacity: past('fade') ? 0 : 1,
        contentOpacity: past('fade') ? 1 : 0,
        formOpacity: stage.value === 'gate' ? 1 : 0,

        envTilt: past('unfold') ? 0 : past('rise') ? 6 : 13,
        envY: past('unfold') ? '110px' : past('rise') ? '26px' : '0px',
        envScale: past('unfold') ? 1.03 : 1,
        envOpacity: past('fade') ? 0 : past('unfold') ? 0.32 : 1,
        groundScale: past('unfold') ? 1.12 : 1,
        groundOpacity: past('fade') ? 0 : past('rise') ? 0.75 : 1,

        flapDeg: past('peel') ? -179 : 0,
        flapShadow: past('rise') ? 0 : past('peel') ? 1 : 0,

        // The letter sits at z-2, behind the pocket front (z-3), so the part
        // still inside the envelope is genuinely hidden by the paper in
        // front of it. It only clears to the top once it is fully drawn out.
        letterZ: past('unfold') ? 9 : 2,
        // The envelope is a preserve-3d scene, so paint order follows real
        // depth, not z-index. Park the letter behind the pocket front's
        // plane while it is inside, and bring it forward once it is out.
        letterDepth: past('unfold') ? 18 : -10,
        // -60% leaves the card straddling the mouth — half out, half still in
        // the pocket — before -88% draws it fully clear to unfold.
        letterY: past('unfold') ? '-84%' : past('rise') ? '-88%' : '0%',
        letterTilt: past('unfold') ? -3 : past('rise') ? -7 : 0,
        letterStretch: past('rise') && ! past('unfold') ? 1.02 : 1,
        letterScale: past('unfold') ? 1.04 : 1,
        letterShadow: past('rise')
            ? '0 24px 38px -22px rgba(39,51,37,.5)'
            : '0 2px 5px -3px rgba(39,51,37,.35)',
        // The folded panels rotate out of the letter's plane, so while the card
        // is still in the pocket they would poke through the envelope front in
        // 3D. They only join once the letter has cleared the mouth.
        panelOpacity: past('rise') ? 1 : 0,
        foldTop: past('unfold') ? 0 : 91,
        foldBot: past('unfold') ? 0 : -92,

        // Once the flap is flat back, it drops below the letter so the paper
        // draws over it as it comes up out of the mouth.
        flapZ: past('rise') ? 1 : 6,

        mouthShadow: past('unfold') ? 0 : past('rise') ? 1 : 0,
        lipOpacity: past('unfold') ? 0 : past('peel') ? 1 : 0,

        // The seal doesn't break. It presses down as it gives, then fades out
        // while the flap peels open underneath it.
        sealPress: past('release') ? 'translateZ(4px) scale(.965)' : 'translateZ(4px) scale(1)',
        sealOpacity: past('peel') ? 0 : 1,
    }))

    return { stage, vals, run, reset }
}
