<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Support\SiteCopy;
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
            'attending.required' => SiteCopy::line('rsvp.error_attending'),
            'party.required' => SiteCopy::line('rsvp.error_party'),
            'party.min' => SiteCopy::line('rsvp.error_party'),
            'party.max' => SiteCopy::line('rsvp.error_party_max', ['count' => $guest->seats]),
            'party.*.name.required' => SiteCopy::line('rsvp.error_name'),
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
            throw new HttpException(403, SiteCopy::line('rsvp.error_locked'));
        }

        return $guest;
    }
}
