<?php

namespace App\Http\Controllers;

use App\Models\Invitation;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $activeColocation = $user->colocations()
            ->wherePivotNull('left_at')
            ->where('status', 'active')
            ->first();

        $pendingInvitations = Invitation::where('email', $user->email)
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('dashboard', compact('activeColocation', 'pendingInvitations'));
    }
}