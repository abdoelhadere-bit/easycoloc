<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Payment;
use Illuminate\Http\Request;

class SettlementController extends Controller
{
    public function markPaid(Request $request, Colocation $colocation)
    {
        $data = $request->validate([
            'to_user_id' => ['required', 'exists:users,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        // Verifier que l'utilisateur connecte est membre actif
        $isMember = $colocation->members()
            ->whereKey(auth()->id())
            ->wherePivotNull('left_at')
            ->exists();

        abort_unless($isMember, 403);

        // Vérifier que le "to_user_id" est membre actif aussi
        $isReceiverMember = $colocation->members()
            ->whereKey($data['to_user_id'])
            ->wherePivotNull('left_at')
            ->exists();

        abort_unless($isReceiverMember, 403);

        // Enregistrer le paiement
        Payment::create([
            'colocation_id' => $colocation->id,
            'from_user_id' => auth()->id(),
            'to_user_id' => $data['to_user_id'],
            'amount' => $data['amount'],
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Paiement enregistré.');
    }
}