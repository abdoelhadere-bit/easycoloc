<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureColocationOwner
{
    public function handle(Request $request, Closure $next)
    {
        $colocation = $request->route('colocation'); 

        abort_unless($colocation && $colocation->isOwner(auth()->id()), 403);

        return $next($request);
    }
}