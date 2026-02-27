<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Invitation;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvitationMail;

class InvitationController extends Controller
{
    public function store(Colocation $colocation)
    {
        $this->authorize('invite', $colocation);
        
        $token = Str::uuid()->toString();

        // Vérifier colocation active
        abort_if($colocation->status !== 'active', 403);

        // Vérifier email déjà membre
        $alreadyMember = $colocation->members()
            ->where('email', request('email'))
            ->wherePivotNull('left_at')
            ->exists();

        if ($alreadyMember) {
            return back()->withErrors([
                'email' => 'Cet utilisateur est déjà membre.'
            ]);
        }

        // Vérifier invitation déjà pending
        $alreadyInvited = Invitation::where('colocation_id', $colocation->id)
            ->where('email', request('email'))
            ->where('status', 'pending')
            ->exists();

        if ($alreadyInvited) {
            return back()->withErrors([
                'email' => 'Une invitation est déjà en attente.'
            ]);
        }

        $invitation = Invitation::create([
            'colocation_id' => $colocation->id,
            'email' => request('email'),
            'token' => $token,
            'status' => 'pending',
        ]);

        Mail::to($invitation->email)
         ->send(new InvitationMail($invitation));

        return back()->with('success', 'Invitation envoyée avec succès.');
    }

    public function show(string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();
        return view('invitations.show', compact('invitation'));
    }

   public function accept(string $token)
{
    $invitation = Invitation::where('token', $token)->firstOrFail();

    if (!auth()->check()) {
        return redirect()->route('login')
            ->withErrors(['auth' => 'Veuillez vous connecter pour accepter l’invitation.']);
    }

    if (auth()->user()->email !== $invitation->email) {
        return redirect()->route('dashboard')
            ->withErrors(['auth' => 'Cette invitation ne vous est pas destinée.']);
    }

    if ($invitation->expires_at && now()->greaterThan($invitation->expires_at)) {
        return redirect()->route('dashboard')
            ->withErrors(['auth' => 'Invitation expirée.']);
    }

    $hasActive = auth()->user()->colocations()
        ->wherePivotNull('left_at')
        ->where('status', 'active')
        ->exists();

    if ($hasActive) {
        return redirect()->route('dashboard')
            ->withErrors(['auth' => 'Vous avez déjà une colocation active.']);
    }

    $colocation = $invitation->colocation;

    $colocation->members()->syncWithoutDetaching([
        auth()->id() => ['role' => 'member']
    ]);

    $invitation->update(['status' => 'accepted']);

    return redirect()->route('colocations.show', $colocation)
        ->with('success', 'Invitation acceptée.');
}

    public function refuse(string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();
        $invitation->update(['status' => 'refused']);
        return redirect()->route('dashboard')->with('success', 'Invitation refusée.');
    }
}
