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
import SiteNav from '@/sections/SiteNav.vue'
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

// Sections the couple has switched off never render, so nothing half-written
// reaches a guest.
const shown = (anchor) => props.invitation?.sections?.[anchor] !== false

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
        <SiteNav :items="invitation.nav" />
        <SiteHeader
            :site="site"
            :guest="invitation.guest"
            :rsvp="invitation.rsvp"
            :show-rsvp-link="shown('rsvp')"
        />
        <HeroImage :src="invitation.heroImage" :alt="invitation.venue.name" />
        <PullQuote :quote="invitation.pullQuote" />
        <TheDay
            v-if="shown('the-day')"
            id="the-day"
            :schedule="invitation.schedule"
            :footnote="invitation.scheduleFootnote"
        />
        <ThePlace v-if="shown('the-place')" id="the-place" :venue="invitation.venue" />
        <GettingThere
            v-if="shown('getting-there')"
            id="getting-there"
            :intro="invitation.travelIntro"
            :options="invitation.travelOptions"
            :footnote="invitation.travelFootnote"
        />
        <WhereToStay
            v-if="shown('where-to-stay')"
            id="where-to-stay"
            :intro="invitation.hotelsIntro"
            :block-code="invitation.blockCode"
            :hotels="invitation.hotels"
        />
        <WhileYoureHere v-if="shown('while-youre-here')" id="while-youre-here" :highlights="invitation.highlights" />
        <Gallery v-if="shown('gallery')" id="gallery" :images="invitation.gallery" />
        <GoodToKnow v-if="shown('good-to-know')" id="good-to-know" :notes="invitation.notes" />
        <Questions v-if="shown('questions')" id="questions" :faqs="invitation.faqs" />
        <Rsvp v-if="shown('rsvp')" id="rsvp" :rsvp="invitation.rsvp" />
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
