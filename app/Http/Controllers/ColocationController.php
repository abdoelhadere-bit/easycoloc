<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreColocationRequest;
use App\Http\Requests\UpdateColocationRequest;
use App\Models\Colocation;
use Illuminate\Http\Request;
use App\Services\BalanceService;
use Illuminate\Support\Str;
use App\Services\ReputationService;
use App\Models\User;
use App\Models\Payment;

class ColocationController extends Controller
{
    public function create()
    {
        return view('colocations.create');
    }

    public function index()
    {
        $user = auth()->user();

        $colocations = $user->colocations()
            ->withPivot(['role', 'joined_at', 'left_at'])
            ->withCount([
                'expenses',
                'members as active_members_count' => function ($q) {
                    $q->whereNull('left_at');
                }
            ])
            ->orderByDesc('colocations.created_at')
            ->get();

        return view('colocations.index', compact('colocations'));
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

    $userId = auth()->id();

    $memberRow = $colocation->members()
        ->where('users.id', $userId)
        ->firstOrFail();

    $pivot = $memberRow->pivot; 

    $readOnly = !is_null($pivot->left_at) || $colocation->status !== 'active';

    $isOwner = !$readOnly && $pivot->role === 'owner';

    // Filtre mois
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
        ->map(fn($d) => substr((string)$d, 0, 7))
        ->unique()
        ->sortDesc()
        ->values();

    $members = $colocation->members()
        ->wherePivotNull('left_at')
        ->get();

    $transactions = $balanceService->calculateBalances($colocation)['transactions'];
    $expensesCount = $expenses->count();
    $expensesTotal = $expenses->sum('amount');

    $categories = $colocation->categories;

    return view('colocations.show', compact(
        'colocation',
        'readOnly',
        'isOwner',
        'pivot',
        'expenses',
        'availableMonths',
        'month',
        'expensesCount',
        'expensesTotal',
        'members',
        'transactions',
        'categories'
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

    public function removeMember(Colocation $colocation, User $member, BalanceService $balanceService)
    {
        $this->authorize('manage', $colocation);

        // ne pas retirer l'owner
        $pivot = $colocation->members()->where('users.id', $member->id)->firstOrFail()->pivot;
        if ($pivot->role === 'owner') {
            return back()->withErrors(['remove' => "Impossible de retirer l'owner."]);
        }

        $balance = $balanceService->getUserBalance($colocation, $member->id);
        $ownerId = $colocation->owner_id;

        if ($balance < 0) {
            Payment::create([
                'colocation_id' => $colocation->id,
                'from_user_id' => $member->id,
                'to_user_id' => $ownerId,
                'amount' => abs($balance),
                'paid_at' => now(),
            ]);
        }

        // Sortie
        $colocation->members()->updateExistingPivot($member->id, [
            'left_at' => now(),
        ]);

        return back()->with('success', 'Membre retiré.');
    }

}