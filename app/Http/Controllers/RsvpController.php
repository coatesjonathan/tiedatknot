<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RsvpController extends Controller
{
    /**
     * Record a reply from the invitation itself. Only an unlocked guest can post
     * one, and the party is capped at the seats we saved for them.
     */
    public function store(Request $request): RedirectResponse
    {
        $guest = $this->unlockedGuest($request);

        $data = $request->validate([
            'attending' => ['required', 'boolean'],
            'party' => ['exclude_if:attending,false', 'required', 'array', 'min:1', 'max:'.$guest->seats],
            'party.*.name' => ['required', 'string', 'max:120'],
            'party.*.dietary' => ['nullable', 'string', 'max:500'],
            'rsvp_note' => ['nullable', 'string', 'max:2000'],
        ], [
            'attending.required' => 'Let us know whether you can make it.',
            'party.required' => 'Tell us who is coming.',
            'party.min' => 'Tell us who is coming.',
            'party.max' => "We've only saved {$guest->seats} ".str('seat')->plural($guest->seats).' for you — send us a note if you need more.',
            'party.*.name.required' => 'We need a name for everyone coming.',
        ]);

        $guest->recordRsvp(
            attending: (bool) $data['attending'],
            party: $data['party'] ?? [],
            note: $data['rsvp_note'] ?? null,
        );

        return back();
    }

    private function unlockedGuest(Request $request): Guest
    {
        $id = $request->session()->get('guest_id');
        $guest = $id ? Guest::find($id) : null;

        if (! $guest) {
            throw new HttpException(403, 'Open your invitation before replying.');
        }

        return $guest;
    }
}
