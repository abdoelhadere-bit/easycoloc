<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Colocation;
use App\Models\Expense;
use App\Models\Payment;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'colocations' => Colocation::count(),
            'expenses_total' => (float) Expense::sum('amount'),
            'payments' => Payment::count(),
            'banned' => User::where('is_banned', true)->count(),
        ];

        $users = User::latest()->paginate(15);

        return view('admin.index', compact('stats', 'users'));
    }

    public function ban(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['ban' => "Vous ne pouvez pas vous bannir vous-même."]);
        }

        $user->update(['is_banned' => true]);

        return back()->with('success', "Utilisateur banni.");
    }

    public function unban(User $user)
    {
        $user->update(['is_banned' => false]);

        return back()->with('success', "Utilisateur débanni.");
    }
}