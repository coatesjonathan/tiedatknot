<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Guest;
use App\Models\Highlight;
use App\Models\Hotel;
use App\Models\Note;
use App\Models\ScheduleItem;
use App\Models\Setting;
use App\Models\TravelOption;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'hello@jonathancoates.co.uk'],
            ['name' => 'Jonny', 'password' => Hash::make('password')],
        );

        Setting::updateOrCreate(['id' => 1], [
            'couple_names' => 'Jenny & Jonny',
            'first_name' => 'Jenny',
            'second_name' => 'Jonny',
            'monogram' => 'JJ',
            'wedding_date' => '2027-05-17',
            'wedding_time' => '18:45',
            'location_label' => 'Granada, Spain',
            'venue_name' => 'Carmen de los Chapiteles',
            'venue_description' => 'A private carmen on the Albaicín hillside, eighty metres below the Generalife: terraced gardens, fountains, and the Alhambra lit up across the valley. Five minutes from the centre of Granada.',
            'venue_address' => "Camino de la Fuente del Avellano, 4\n18010 Granada, Spain",
            'venue_travel_note' => "The lane is narrow and steep, and there's almost no parking — take a taxi from the centre (about €8, ten minutes) and we'll sort taxis home at the end of the night.",
            'maps_url' => 'https://maps.google.com/?q=Carmen+de+los+Chapiteles,+Camino+de+la+Fuente+del+Avellano+4,+18010+Granada',
            'contact_email' => 'jennyandjonny@example.com',
            'rsvp_deadline' => '2027-02-01',
            'block_code' => 'JENNY & JONNY 2027',
            'honeymoon_url' => null,
            'hero_image_path' => 'wedding/venue.png',
            'venue_image_path' => null,
            'pull_quote' => "We're getting married in a walled garden below the Alhambra — and it's a long way to ask you to come, which is exactly why we want you there.",
            'travel_intro' => "Most of you are coming from the UK or the US. There's no need to fly into Granada itself — the trains are excellent, and the ride down through the mountains is part of the trip.",
            'travel_footnote' => "Coming a few days early? Say so when you reply and we'll point you at the good bits.",
            'hotels_intro' => "We've held rooms at our favourite places at an agreed rate. Quote :code when you book, before the release date below.",
            'schedule_footnote' => 'Times still to be confirmed with the venue.',
            'rsvp_body' => "Tell us whether you can make it, who's coming with you, and anything we should know about food. No forms, no logins.",
            'animation_speed' => 'quick',
            'countdown_enabled' => true,
        ]);

        $this->seed(ScheduleItem::class, 'time', [
            ['time' => '18:00', 'title' => 'Arrival & welcome drinks', 'detail' => 'On the Mirador terrace, facing the Alhambra'],
            ['time' => '18:45', 'title' => 'Ceremony', 'detail' => 'Please be seated by 18:35'],
            ['time' => '19:30', 'title' => 'Cocktails in the patio', 'detail' => 'Tapas among the old fountains'],
            ['time' => '21:00', 'title' => 'Dinner', 'detail' => 'Salón de Cristal, with the city below'],
            ['time' => '23:30', 'title' => 'Dancing', 'detail' => 'Until the last of us gives in'],
        ]);

        $this->seed(TravelOption::class, 'title', [
            [
                'label' => 'Option one',
                'title' => 'Via Madrid',
                'body' => 'Fly into Madrid-Barajas (MAD) — the easiest arrival from the US. Metro or Cercanías to Atocha, then the AVE straight to Granada: around 3h20, roughly €40–70 if you book early.',
                'footnote' => 'Book at [renfe.com](https://www.renfe.com/es/en) · tickets open ~3 months ahead',
            ],
            [
                'label' => 'Option two',
                'title' => 'Via Málaga',
                'body' => "Usually the cheapest from the UK. Málaga (AGP) has a train station under the airport; change at María Zambrano for Granada — about 1h45 by Avant, or 2h by bus if the train times don't work.",
                'footnote' => 'Plenty of direct flights from London, Manchester, Edinburgh and Dublin.',
            ],
            [
                'label' => 'Option three',
                'title' => 'Straight in',
                'body' => "Granada's own airport (GRX) is 20 minutes from the city, with connections from Madrid, Barcelona and a few seasonal European routes. Small, quiet, and very easy.",
                'footnote' => 'Airport bus into the centre, €3.',
            ],
        ]);

        // Placeholder until the couple confirm — edit in the admin panel.
        $this->seed(Hotel::class, 'label', [
            ['label' => 'Our favourite', 'name' => '[ Hotel name ]', 'description' => '[ A line on why we love it — the roof terrace, the view, the breakfast. ]', 'rate' => '€[ 000 ] / night', 'rooms_held' => '[ 00 ] rooms held'],
            ['label' => 'Best value', 'name' => '[ Hotel name ]', 'description' => '[ Walking distance to the old town, simple and lovely. ]', 'rate' => '€[ 000 ] / night', 'rooms_held' => '[ 00 ] rooms held'],
            ['label' => 'For families', 'name' => '[ Hotel name ]', 'description' => '[ Bigger rooms, a pool, and a short taxi to the venue. ]', 'rate' => '€[ 000 ] / night', 'rooms_held' => '[ 00 ] rooms held'],
        ]);

        $this->seed(Highlight::class, 'title', [
            ['title' => 'The Alhambra', 'body' => 'Book the moment tickets release — months ahead, not weeks. Go early or go at sunset.'],
            ['title' => 'Albaicín & Sacromonte', 'body' => 'Get lost in the white streets, end up at Mirador de San Nicolás for the view.'],
            ['title' => 'Free tapas', 'body' => "Granada still gives you food with every drink. Order a second round; that's dinner."],
            ['title' => 'Beyond the city', 'body' => 'The Sierra Nevada in an hour, the coast in ninety minutes. Both worth a day.'],
        ]);

        $this->seed(Note::class, 'title', [
            ['title' => 'What to wear', 'body' => 'Summer formal, sensibly. Most of the evening is outside on gravel and stone — bring shoes you can stand up in, and something for when the sun drops behind the hill.'],
            ['title' => 'Gifts', 'body' => "You're flying to Spain for us — that's the gift. If you'd still like to give something, there's a small honeymoon fund: [ link to follow ]."],
            ['title' => 'Weather', 'body' => 'Mid-May in Granada is usually mid-20s and dry by day, cooler in the evening. Sunglasses for the ceremony, a jacket for the dancing.'],
        ]);

        $this->seed(Faq::class, 'question', [
            ['question' => 'Can I bring a plus one?', 'answer' => "Your invitation says how many seats we've saved for you. If you'd like to bring someone else, ask us — we'll do our best."],
            ['question' => 'Are children welcome?', 'answer' => "[ Tell me your line on this and I'll write it properly. ]"],
            ['question' => 'How do I get to the venue on the day?', 'answer' => "Taxi from anywhere central: ten minutes, around €8. Ask for Camino de la Fuente del Avellano. Don't walk it in wedding shoes — it's uphill."],
            ['question' => 'What if it rains?', 'answer' => 'Unlikely in May, but the venue has beautiful indoor rooms and the whole day moves inside without drama.'],
            ['question' => 'Anything else?', 'answer' => "Just ask us. Honestly, we'd rather answer twice than have you wondering."],
        ]);

        foreach ([
            ['name' => 'Sam', 'email' => 'sam@example.com', 'seats' => 2],
            ['name' => 'the Coates family', 'email' => 'family@example.com', 'seats' => 4],
            ['name' => 'you', 'email' => 'you@example.com', 'seats' => 1],
        ] as $guest) {
            Guest::updateOrCreate(['email' => $guest['email']], $guest);
        }
    }

    /** Upsert an ordered content list, keyed on the column that identifies a row. */
    private function seed(string $model, string $key, array $rows): void
    {
        foreach ($rows as $index => $row) {
            $model::updateOrCreate(
                [$key => $row[$key]],
                [...$row, 'sort_order' => $index, 'is_published' => true],
            );
        }
    }
}
