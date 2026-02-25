<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Invitation;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    public function store(Colocation $colocation)
    {
        // TODO: vérifier owner (plus tard policy)
        $token = Str::uuid()->toString();

        $invitation = Invitation::create([
            'colocation_id' => $colocation->id,
            'email' => request('email'),
            'token' => $token,
            'status' => 'pending',
        ]);

        // Pour l’instant, on affiche le lien au lieu d’envoyer email
        return back()->with('invite_link', route('invitations.show', $token));
    }

    public function show(string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();
        return view('invitations.show', compact('invitation'));
    }

    public function accept(string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        abort_unless(auth()->check(), 403);

        // Vérifier email correspond
        abort_unless(auth()->user()->email === $invitation->email, 403);

        // Vérifier 1 seule colocation active
        $hasActive = auth()->user()->colocations()
            ->wherePivotNull('left_at')
            ->where('status', 'active')
            ->exists();

        abort_if($hasActive, 403);

        $colocation = $invitation->colocation;

        // Attacher dans pivot
        $colocation->members()->syncWithoutDetaching([
            auth()->id() => ['role' => 'member']
        ]);

        $invitation->update(['status' => 'accepted']);

        return redirect()->route('colocations.show', $colocation);
    }

    public function refuse(string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();
        $invitation->update(['status' => 'refused']);
        return redirect()->route('dashboard')->with('success', 'Invitation refusée.');
    }
}
