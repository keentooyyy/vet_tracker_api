<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAccountType
{
    public function handle(Request $request, Closure $next, $type)
    {
        $user = Auth::user();

        if (!$user || $user->account_type !== $type) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}
