<?php

namespace App\Http\Middleware;

use App\Support\AdminAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAbility
{
    public function handle(Request $request, Closure $next, string $ability): Response
    {
        if (AdminAccess::can($request->user(), $ability)) {
            return $next($request);
        }

        abort(403);
    }
}
