<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GateController extends Controller
{
    /**
     * Check the address against the guest list. On success the unlock is kept in
     * the session and the page re-renders with the invitation props attached —
     * the client keeps the gate on screen while the envelope animation plays.
     */
    public function unlock(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'max:255', 'regex:/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/'],
        ], [
            'email.required' => 'That does not look like an email address.',
            'email.regex' => 'That does not look like an email address.',
        ]);

        $guest = Guest::findByEmail($data['email']);

        if (! $guest) {
            throw ValidationException::withMessages([
                'email' => "We can't find that address on our list — try the one the invitation came to.",
            ]);
        }

        $guest->recordUnlock();

        $request->session()->put('guest_id', $guest->id);

        return back();
    }

    /** "Open the letter again" — clear the unlock and replay from the gate. */
    public function lock(Request $request): RedirectResponse
    {
        $request->session()->forget('guest_id');

        return back();
    }
}
