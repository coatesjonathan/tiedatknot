<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Setting;
use App\Support\InvitationPayload;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InvitationController extends Controller
{
    public function __construct(private readonly InvitationPayload $payload) {}

    /**
     * One page, two states. The invitation props are withheld entirely until the
     * guest has unlocked, so the content never sits in the HTML behind the gate.
     */
    public function __invoke(Request $request): Response
    {
        $settings = Setting::current();
        $guest = $this->unlockedGuest($request);

        return Inertia::render('Invitation', [
            'site' => $this->payload->site($settings),
            'unlocked' => (bool) $guest,
            'invitation' => $guest
                ? $this->payload->invitation($settings, $guest)
                : null,
        ]);
    }

    private function unlockedGuest(Request $request): ?Guest
    {
        $id = $request->session()->get('guest_id');

        return $id ? Guest::with('attendees')->find($id) : null;
    }
}
