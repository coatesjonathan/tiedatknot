<script setup>
import { computed, provide, ref, watch } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import { lock, unlock } from '@/actions/App/Http/Controllers/GateController'
import Gate from '@/components/Gate.vue'
import { COPY_KEY } from '@/composables/useCopy'
import { useEnvelopeSequence } from '@/composables/useEnvelopeSequence'
import Gallery from '@/sections/Gallery.vue'
import GettingThere from '@/sections/GettingThere.vue'
import GoodToKnow from '@/sections/GoodToKnow.vue'
import HeroImage from '@/sections/HeroImage.vue'
import PullQuote from '@/sections/PullQuote.vue'
import Questions from '@/sections/Questions.vue'
import Rsvp from '@/sections/Rsvp.vue'
import SiteFooter from '@/sections/SiteFooter.vue'
import SiteHeader from '@/sections/SiteHeader.vue'
import TheDay from '@/sections/TheDay.vue'
import ThePlace from '@/sections/ThePlace.vue'
import WhereToStay from '@/sections/WhereToStay.vue'
import WhileYoureHere from '@/sections/WhileYoureHere.vue'

const props = defineProps({
    site: { type: Object, required: true },
    unlocked: { type: Boolean, default: false },
    invitation: { type: Object, default: null },
})

// Admin-editable wording. The gate only ever receives its own lines; the rest
// arrives with the invitation props once the guest has unlocked.
provide(
    COPY_KEY,
    computed(() => ({ ...(props.site.copy ?? {}), ...(props.invitation?.copy ?? {}) })),
)

// A returning guest lands straight on the invitation; everyone else starts sealed.
const { stage, vals, run, reset } = useEnvelopeSequence(props.site.animationSpeed, props.unlocked)

const form = useForm({ email: '' })

const error = computed(() => form.errors.email ?? '')

const submit = () =>
    form.submit(unlock(), {
        preserveState: true,
        preserveScroll: true,
        // The invitation props arrive now, but the gate stays on top of them
        // until the sequence cross-fades at `fade`. Park the page at the top so
        // the guest meets the header, not wherever the browser left the scroll.
        onSuccess: () => {
            window.scrollTo(0, 0)
            run()
        },
    })

const replay = () =>
    router.delete(lock().url, {
        preserveState: true,
        onSuccess: () => {
            window.scrollTo(0, 0)
            form.reset()
            form.clearErrors()
            reset()
        },
    })

// Keep the gate's scroll lock honest while the sequence is running.
watch(
    () => vals.value.gateOn,
    (gated) => {
        document.documentElement.style.overflow = gated && stage.value !== 'gate' ? 'hidden' : ''
    },
)

const email = ref('')

watch(email, (value) => {
    form.email = value
    if (form.errors.email) form.clearErrors('email')
})
</script>

<template>
    <Head :title="`${site.coupleNames} · ${site.locationLabel}`" />

    <!-- The invitation is only in the DOM once the server has unlocked it. -->
    <div
        v-if="invitation"
        class="relative min-h-screen bg-cream transition-opacity duration-[800ms] ease-[ease]"
        :style="{ opacity: vals.contentOpacity }"
        :inert="! vals.contentOn"
    >
        <SiteHeader :site="site" :guest="invitation.guest" />
        <HeroImage :src="invitation.heroImage" :alt="invitation.venue.name" />
        <PullQuote :quote="invitation.pullQuote" />
        <TheDay :schedule="invitation.schedule" :footnote="invitation.scheduleFootnote" />
        <ThePlace :venue="invitation.venue" />
        <GettingThere
            :intro="invitation.travelIntro"
            :options="invitation.travelOptions"
            :footnote="invitation.travelFootnote"
        />
        <WhereToStay :intro="invitation.hotelsIntro" :block-code="invitation.blockCode" :hotels="invitation.hotels" />
        <WhileYoureHere :highlights="invitation.highlights" />
        <Gallery :images="invitation.gallery" />
        <GoodToKnow :notes="invitation.notes" />
        <Questions :faqs="invitation.faqs" />
        <Rsvp :rsvp="invitation.rsvp" />
        <SiteFooter :site="site" @replay="replay" />
    </div>

    <Gate
        v-if="vals.gateOn"
        v-model:email="email"
        :vals="vals"
        :stage="stage"
        :site="site"
        :error="error"
        :processing="form.processing"
        @submit="submit"
    />
</template>
