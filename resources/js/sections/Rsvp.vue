<script setup>
import { computed, ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { store } from '@/actions/App/Http/Controllers/RsvpController'
import RevealSection from '@/components/RevealSection.vue'

const props = defineProps({ rsvp: { type: Object, required: true } })

const mailto = computed(() => `mailto:${props.rsvp.email}?subject=${encodeURIComponent(props.rsvp.subject)}`)

const replied = computed(() => props.rsvp.status !== 'pending')
const isAttending = computed(() => props.rsvp.status === 'attending')

const blankPerson = (name = '') => ({ name, dietary: '' })

// The first row is the guest we invited; the rest are their plus ones.
const startingParty = () =>
    props.rsvp.party.length ? props.rsvp.party.map((person) => ({ ...person })) : [blankPerson(props.rsvp.guestName ?? '')]

const form = useForm({
    attending: replied.value ? isAttending.value : null,
    party: startingParty(),
    rsvp_note: props.rsvp.note ?? '',
})

// A guest who has already replied sees their answer back; the form returns when
// they ask to change it.
const editing = ref(! replied.value)

const seatsLeft = computed(() => props.rsvp.seats - form.party.length)

const choose = (attending) => {
    form.attending = attending
    form.clearErrors()

    if (attending && form.party.length === 0) {
        form.party = [blankPerson(props.rsvp.guestName ?? '')]
    }
}

const addPerson = () => {
    if (seatsLeft.value > 0) form.party.push(blankPerson())
}

const removePerson = (index) => {
    form.party.splice(index, 1)
    form.clearErrors(`party.${index}.name`)
}

const submit = () =>
    form
        .transform((data) => (data.attending ? data : { ...data, party: [] }))
        .post(store().url, {
            preserveScroll: true,
            onSuccess: () => {
                editing.value = false
            },
        })

// The fresh reply arrives as new props; re-seed the form so "change your reply"
// starts from what we just saved.
watch(
    () => props.rsvp,
    (rsvp) => {
        form.defaults({
            attending: rsvp.status === 'pending' ? null : rsvp.status === 'attending',
            party: startingParty(),
            rsvp_note: rsvp.note ?? '',
        })
        form.reset()
    },
)

const personError = (index, field) => form.errors[`party.${index}.${field}`]

const answers = [
    { value: true, label: 'Yes, count us in' },
    { value: false, label: "Sadly we can't" },
]

const fieldClass =
    'w-full rounded-sheet border border-paper/35 bg-paper/10 px-[14px] py-[12px] text-[16px] font-light text-paper placeholder:text-paper/45 focus:border-paper/70'
</script>

<template>
    <RevealSection id="rsvp" class="mt-[90px] bg-terracotta px-[24px] pb-[96px] pt-[88px] text-center">
        <p v-if="rsvp.deadlineLabel" class="m-0 text-[12px] font-medium uppercase tracking-[.3em] text-paper/70">
            Please reply by {{ rsvp.deadlineLabel }}
        </p>

        <h2 class="mt-[20px] font-serif text-[clamp(38px,6vw,68px)] font-normal leading-[1.05] text-paper">
            Will you be there?
        </h2>

        <p class="mx-auto mt-[18px] max-w-[520px] text-[17px] font-light leading-[1.7] text-paper/85">
            {{ rsvp.body }}
        </p>

        <!-- What we have on file, once they have replied. -->
        <div
            v-if="replied && ! editing"
            class="mx-auto mt-[34px] max-w-[520px] rounded-sheet border border-paper/30 bg-paper/10 px-[26px] py-[28px] text-paper"
        >
            <p class="m-0 text-[12px] font-medium uppercase tracking-[.28em] text-paper/70">
                Replied{{ rsvp.repliedAtLabel ? ` ${rsvp.repliedAtLabel}` : '' }}
            </p>

            <p class="mt-[14px] font-serif text-[26px] leading-[1.25]">
                {{ isAttending ? "You're coming — wonderful." : "You can't make it." }}
            </p>

            <ul v-if="isAttending && rsvp.party.length" class="m-0 mt-[18px] list-none space-y-[8px] p-0 text-left">
                <li
                    v-for="person in rsvp.party"
                    :key="person.name"
                    class="border-t border-paper/20 pt-[8px] text-[16px] font-light"
                >
                    {{ person.name }}
                    <span v-if="person.dietary" class="text-paper/70">— {{ person.dietary }}</span>
                </li>
            </ul>

            <p v-if="rsvp.note" class="mt-[18px] text-left text-[15px] font-light italic leading-[1.6] text-paper/80">
                “{{ rsvp.note }}”
            </p>

            <button
                type="button"
                class="mt-[22px] cursor-pointer border-0 border-b border-paper/50 bg-transparent p-0 text-[13px] font-medium uppercase tracking-[.22em] text-paper"
                @click="editing = true"
            >
                Change your reply
            </button>
        </div>

        <form v-else class="mx-auto mt-[34px] max-w-[520px] text-left" @submit.prevent="submit">
            <div class="flex flex-col gap-[12px] sm:flex-row">
                <button
                    v-for="option in answers"
                    :key="String(option.value)"
                    type="button"
                    class="flex-1 cursor-pointer rounded-sheet border px-[20px] py-[16px] text-[13px] font-medium uppercase tracking-[.2em]"
                    :class="
                        form.attending === option.value
                            ? 'border-paper bg-paper text-terracotta'
                            : 'border-paper/40 bg-transparent text-paper'
                    "
                    @click="choose(option.value)"
                >
                    {{ option.label }}
                </button>
            </div>

            <p v-if="form.errors.attending" class="mt-[12px] text-center text-[15px] font-light text-paper">
                {{ form.errors.attending }}
            </p>

            <!-- Who is coming. The seats we saved cap the party. -->
            <div v-if="form.attending === true" class="mt-[28px]">
                <p class="m-0 text-[12px] font-medium uppercase tracking-[.28em] text-paper/70">
                    Who's coming
                </p>

                <p class="mt-[8px] text-[15px] font-light leading-[1.6] text-paper/75">
                    {{ rsvp.seats === 1 ? "We've saved a seat for you." : `We've saved ${rsvp.seats} seats for you.` }}
                    Give us each name so we can write the place cards, and tell us about anything you can't eat.
                </p>

                <div
                    v-for="(person, index) in form.party"
                    :key="index"
                    class="mt-[16px] border-t border-paper/20 pt-[16px]"
                >
                    <div class="flex items-baseline justify-between gap-[12px]">
                        <label class="text-[12px] font-medium uppercase tracking-[.22em] text-paper/70">
                            {{ index === 0 ? 'You' : `Guest ${index + 1}` }}
                        </label>
                        <button
                            v-if="index > 0"
                            type="button"
                            class="cursor-pointer border-0 bg-transparent p-0 text-[13px] font-light text-paper/70 underline"
                            @click="removePerson(index)"
                        >
                            Remove
                        </button>
                    </div>

                    <input
                        v-model="person.name"
                        type="text"
                        :placeholder="index === 0 ? 'Your name' : 'Their full name'"
                        :class="[fieldClass, 'mt-[10px]']"
                    />
                    <p v-if="personError(index, 'name')" class="mt-[6px] text-[14px] font-light text-paper">
                        {{ personError(index, 'name') }}
                    </p>

                    <input
                        v-model="person.dietary"
                        type="text"
                        placeholder="Dietary requirements — allergies, vegetarian, none"
                        :class="[fieldClass, 'mt-[10px]']"
                    />
                    <p v-if="personError(index, 'dietary')" class="mt-[6px] text-[14px] font-light text-paper">
                        {{ personError(index, 'dietary') }}
                    </p>
                </div>

                <button
                    v-if="seatsLeft > 0"
                    type="button"
                    class="mt-[18px] cursor-pointer rounded-sheet border border-dashed border-paper/45 bg-transparent px-[18px] py-[12px] text-[13px] font-medium uppercase tracking-[.2em] text-paper"
                    @click="addPerson"
                >
                    Add someone — {{ seatsLeft }} {{ seatsLeft === 1 ? 'seat' : 'seats' }} left
                </button>

                <p v-if="form.errors.party" class="mt-[12px] text-[15px] font-light text-paper">
                    {{ form.errors.party }}
                </p>
            </div>

            <div v-if="form.attending !== null" class="mt-[28px]">
                <label for="rsvp-note" class="text-[12px] font-medium uppercase tracking-[.28em] text-paper/70">
                    Anything else?
                </label>
                <textarea
                    id="rsvp-note"
                    v-model="form.rsvp_note"
                    rows="4"
                    placeholder="Songs we have to play, who you'd like to sit with, when you're arriving — anything at all."
                    :class="[fieldClass, 'mt-[10px] resize-y']"
                ></textarea>
                <p v-if="form.errors.rsvp_note" class="mt-[6px] text-[14px] font-light text-paper">
                    {{ form.errors.rsvp_note }}
                </p>
            </div>

            <button
                type="submit"
                :disabled="form.processing || form.attending === null"
                class="mt-[28px] w-full cursor-pointer rounded-sheet bg-paper px-[34px] py-[16px] text-[13px] font-medium uppercase tracking-[.26em] text-terracotta disabled:opacity-50"
            >
                {{ form.processing ? 'Sending…' : replied ? 'Update our reply' : 'Send our reply' }}
            </button>

            <button
                v-if="replied"
                type="button"
                class="mt-[14px] w-full cursor-pointer border-0 bg-transparent p-0 text-[13px] font-light text-paper/75 underline"
                @click="editing = false"
            >
                Never mind, keep what we said
            </button>
        </form>

        <p class="mt-[30px] text-[15px] font-light text-paper/75">
            Rather write to us? We're at
            <a :href="mailto" class="border-b border-paper/50 text-paper [overflow-wrap:anywhere]">{{ rsvp.email }}</a>
        </p>
    </RevealSection>
</template>
