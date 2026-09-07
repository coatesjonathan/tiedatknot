<?php

namespace App\Support;

use App\Models\Faq;
use App\Models\GalleryImage;
use App\Models\Guest;
use App\Models\Highlight;
use App\Models\Hotel;
use App\Models\Note;
use App\Models\ScheduleItem;
use App\Models\Setting;
use App\Models\TravelOption;
use Illuminate\Support\Facades\Storage;

/**
 * Builds the two prop bundles the invitation page needs: `site`, which the gate
 * may see, and `invitation`, which is only ever sent to an unlocked guest.
 */
class InvitationPayload
{
    public function site(Setting $settings): array
    {
        return [
            'coupleNames' => $settings->couple_names,
            'firstName' => $settings->first_name,
            'secondName' => $settings->second_name,
            'monogram' => $settings->monogram,
            'weddingDate' => $settings->wedding_date?->toDateString(),
            'weddingDateLabel' => $settings->wedding_date?->format('j F Y'),
            'weddingTime' => $settings->wedding_time,
            'locationLabel' => $settings->location_label,
            'venueName' => $settings->venue_name,
            'contactEmail' => $settings->contact_email,
            'animationSpeed' => $settings->animation_speed,
            'countdownEnabled' => $settings->countdown_enabled,
            'eyebrow' => trim(mb_strtoupper(
                str($settings->location_label)->before(',')->toString().' · '.$settings->wedding_date?->format('Y')
            ), ' ·'),
        ];
    }

    public function invitation(Setting $settings, ?Guest $guest): array
    {
        return [
            'guest' => $guest ? [
                'name' => $guest->name,
                'seats' => $guest->seats,
                'greeting' => $guest->name ? 'Dear '.$guest->name : 'You are invited',
                'seatLine' => $guest->seatLine(),
            ] : null,

            'pullQuote' => $settings->pull_quote,
            'heroImage' => $this->url($settings->hero_image_path),
            'venue' => [
                'name' => $settings->venue_name,
                'description' => $settings->venue_description,
                'address' => $settings->venue_address,
                'travelNote' => $settings->venue_travel_note,
                'mapsUrl' => $settings->maps_url,
                'image' => $this->url($settings->venue_image_path),
            ],

            'schedule' => ScheduleItem::published()->get(['time', 'title', 'detail']),
            'scheduleFootnote' => $settings->schedule_footnote,

            'travelIntro' => $settings->travel_intro,
            'travelFootnote' => $settings->travel_footnote,
            'travelOptions' => TravelOption::published()->get(['label', 'title', 'body', 'footnote']),

            'hotelsIntro' => $settings->hotels_intro,
            'blockCode' => $settings->block_code,
            'hotels' => Hotel::published()->get()->map(fn (Hotel $hotel) => [
                'label' => $hotel->label,
                'name' => $hotel->name,
                'description' => $hotel->description,
                'rate' => $hotel->rate,
                'roomsHeld' => $hotel->rooms_held,
                'releaseDate' => $hotel->release_date?->format('j M Y'),
                'bookingUrl' => $hotel->booking_url,
                'image' => $this->url($hotel->image_path),
            ])->values(),

            'highlights' => Highlight::published()->get(['title', 'body']),
            'notes' => Note::published()->get(['title', 'body']),
            'faqs' => Faq::published()->get(['question', 'answer']),

            'gallery' => GalleryImage::published()->get()->map(fn (GalleryImage $image) => [
                'image' => $this->url($image->image_path),
                'caption' => $image->caption,
                'isWide' => $image->is_wide,
            ])->values(),

            'rsvp' => [
                'deadlineLabel' => $settings->rsvp_deadline?->format('j F Y'),
                'body' => $settings->rsvp_body,
                'email' => $settings->contact_email,
                'subject' => 'RSVP — '.$settings->couple_names.', '.$settings->wedding_date?->format('j F Y'),
                'seats' => $guest?->seats ?? 1,
                'guestName' => $guest?->name,
                'status' => $guest?->rsvp_status ?? 'pending',
                'party' => $guest?->partyForForm() ?? [],
                'note' => $guest?->rsvp_note,
                'repliedAtLabel' => $guest?->rsvp_submitted_at?->format('j F Y'),
            ],

            'honeymoonUrl' => $settings->honeymoon_url,
        ];
    }

    private function url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        return str_starts_with($path, 'http') ? $path : Storage::disk('public')->url($path);
    }
}
