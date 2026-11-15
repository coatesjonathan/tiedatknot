<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Setting;
use App\Support\SiteCopy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
        $mealLabels = Setting::current()->mealLabels();

        $data = $request->validate([
            'attending' => ['required', 'boolean'],
            'party' => ['exclude_if:attending,false', 'required', 'array', 'min:1', 'max:'.$guest->seats],
            'party.*.name' => ['required', 'string', 'max:120'],
            'party.*.meal' => $mealLabels
                ? ['required', 'string', Rule::in($mealLabels)]
                : ['prohibited'],
            'party.*.dietary' => ['nullable', 'string', 'max:500'],
            'rsvp_note' => ['nullable', 'string', 'max:2000'],
        ], [
            'attending.required' => SiteCopy::line('rsvp.error_attending'),
            'party.required' => SiteCopy::line('rsvp.error_party'),
            'party.min' => SiteCopy::line('rsvp.error_party'),
            'party.max' => SiteCopy::line('rsvp.error_party_max', ['count' => $guest->seats]),
            'party.*.name.required' => SiteCopy::line('rsvp.error_name'),
            'party.*.meal.required' => SiteCopy::line('rsvp.error_meal'),
            'party.*.meal.in' => SiteCopy::line('rsvp.error_meal'),
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
