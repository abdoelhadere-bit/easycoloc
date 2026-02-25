<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreColocationRequest;
use App\Http\Requests\UpdateColocationRequest;
use App\Models\Colocation;
use Illuminate\Http\Request;
use App\Services\BalanceService;

class ColocationController extends Controller
{
    public function create()
    {
        return view('colocations.create');
    }

    public function store(StoreColocationRequest $request)
    {
        $user = auth()->user();

        // seule colocation active par utilisateur
        $hasActive = $user->colocations()
            ->wherePivotNull('left_at')
            ->where('status', 'active')
            ->exists();

        if ($hasActive) {
            return back()->withErrors([
                'colocation' => 'Vous avez déjà une colocation active.',
            ])->withInput();
        }

        // creer colocation comme owner
        $colocation = Colocation::create([
            'name' => $request->name,
            'status' => 'active',
            'owner_id' => $user->id,
        ]);

        // Attacher owner dans pivot
        $colocation->members()->attach($user->id, [
            'role' => 'owner',
            'left_at' => null,
        ]);

        return redirect()->route('colocations.show', $colocation);
    }




  public function show(Colocation $colocation, BalanceService $balanceService)
    {
        // Verifier que l utilisateur est membre actif
        $isMember = $colocation->members()
            ->whereKey(auth()->id())
            ->wherePivotNull('left_at')
            ->exists();

        $isActive = $colocation->status;
        if($isActive === 'cancelled'){
            // abort(403);
        }
        abort_unless($isMember, 403);

        $expenses = $colocation->expenses()
            ->with(['category', 'payer'])
            ->latest('date')
            ->get();

        $members = $colocation->members()
            ->wherePivotNull('left_at')
            ->get();

        $data = $balanceService->calculate($colocation);

        $balances = $data['balances'];
        $transactions = $data['transactions'];

        return view('colocations.show', compact(
            'colocation',
            'expenses',
            'members',
            'balances',
            'transactions'
        ));
    }




    public function update(UpdateColocationRequest $request, Colocation $colocation)
    {
        // (Plus tard: vérifier owner)
        $colocation->update([
            'name' => $request->name,
        ]);

        return back()->with('success', 'Colocation mise à jour.');
    }


    public function cancel(Colocation $colocation)
    {
        // (Plus tard: vérifier owner)
        $colocation->update(['status' => 'cancelled']);
        return redirect()->route('dashboard')->with('success', 'Colocation annulée.');
    }

    
    public function leave(Colocation $colocation)
    {
        // owner ne peut pas quitter
        $pivot = $colocation->members()
            ->where('users.id', auth()->id())
            ->firstOrFail()
            ->pivot;

        if ($pivot->role === 'owner') {
            return back()->withErrors(['leave' => 'Le owner ne peut pas quitter la colocation.']);
        }

        $colocation->members()->updateExistingPivot(auth()->id(), [
            'left_at' => now(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Vous avez quitté la colocation.');
    }
}