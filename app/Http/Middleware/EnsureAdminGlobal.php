<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAdminGlobal
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin_global', 403);

        return $next($request);
    }
}