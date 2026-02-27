<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreColocationRequest;
use App\Http\Requests\UpdateColocationRequest;
use App\Models\Colocation;
use Illuminate\Http\Request;
use App\Services\BalanceService;
use Illuminate\Support\Str;

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
        
        $this->authorize('view', $colocation);

        $month = request('month'); 

        $expensesQuery = $colocation->expenses()
            ->with(['category', 'payer'])
            ->latest('date');

        if ($month) {
            $expensesQuery->where('date', 'like', $month.'-%');
        }

        $expenses = $expensesQuery->get();

        $availableMonths = $colocation->expenses()
                                ->pluck('date')
                                ->map(fn($d) => substr($d, 0, 7)) 
                                ->unique()
                                ->sortDesc()
                                ->values();

        $members = $colocation->members()
            ->wherePivotNull('left_at')
            ->get();

        $data = $balanceService->calculate($colocation);
        
        $balances = $data['balances'];
        // dd($data['balances']);
        $transactions = $data['transactions'];

        $total = $colocation->expenses->sum('amount');
        $count = $members->count();
        $share = $count > 0 ? $total / $count : 0;

        $categories = $colocation->categories;

        $statsByCategory = $expenses->groupBy('category_id')
                                ->map(function ($items) {
                                    return $items->sum('amount');  
                                });

        return view('colocations.show', compact(
            'categories',
            'colocation',
            'expenses',
            'members',
            'balances',
            'transactions',
            'total',
            'share',
            'statsByCategory',
            'availableMonths',
            'month'
        ));
    }




    public function update(UpdateColocationRequest $request, Colocation $colocation)
    {
        $this->authorize('update', $colocation);

        $colocation->update([
            'name' => $request->name,
        ]);

        return back()->with('success', 'Colocation mise à jour.');
    }


    public function cancel(Colocation $colocation, ReputationService $reputationService) 
    {
        $this->authorize('delete', $colocation);

        $reputationService->handleCancel($colocation);

        $colocation->update(['status' => 'cancelled']);

        return redirect()->route('dashboard')
            ->with('success', 'Colocation annulée.');
    }

    
    public function leave(Colocation $colocation, ReputationService $reputationService) 
    {
        $user = auth()->user();

        $pivot = $colocation->members()
            ->where('users.id', $user->id)
            ->firstOrFail()
            ->pivot;

        if ($pivot->role === 'owner') {
            return back()->withErrors(['leave' => 'Le owner ne peut pas quitter la colocation.']);
        }

        $reputationService->handleLeave($colocation, $user);

        $colocation->members()->updateExistingPivot($user->id, [
            'left_at' => now(),
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Vous avez quitté la colocation.');
    }

    public function removeMember(Colocation $colocation, User $member, ReputationService $reputationService) 
    {
        $this->authorize('update', $colocation);

        $owner = auth()->user();

        $reputationService->handleOwnerRemove(
            $colocation,
            $owner,
            $member
        );

        $colocation->members()->updateExistingPivot($member->id, [
            'left_at' => now(),
        ]);

        return back()->with('success', 'Membre retiré.');
    }
}