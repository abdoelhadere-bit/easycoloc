<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Expense;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Colocation active
        $activeColocation = $user->colocations()
            ->wherePivotNull('left_at')
            ->where('status', 'active')
            ->with(['members' => function ($q) {
                $q->wherePivotNull('left_at');
            }])
            ->first();

        // Invitations reçues
        $pendingInvitations = Invitation::with('colocation')
            ->where('email', $user->email)
            ->where('status', 'pending')
            ->latest()
            ->get();

        // Stats “Réputation”
        $reputation = (int) ($user->reputation ?? 0);

        // Dépenses globales du mois en cours (toutes colocations où le user est membre actif)
        $monthStart = Carbon::now()->startOfMonth()->toDateString();
        $monthEnd   = Carbon::now()->endOfMonth()->toDateString();

        $activeColocIds = $user->colocations()
            ->wherePivotNull('left_at')
            ->pluck('colocations.id');

        $monthlyTotal = Expense::whereIn('colocation_id', $activeColocIds)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->sum('amount');

        $recentExpenses = Expense::with(['payer', 'category', 'colocation'])
            ->whereIn('colocation_id', $activeColocIds)
            ->latest('date')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'activeColocation',
            'pendingInvitations',
            'reputation',
            'monthlyTotal',
            'recentExpenses'
        ));
    }
}