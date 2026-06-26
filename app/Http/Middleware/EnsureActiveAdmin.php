<?php

namespace App\Http\Middleware;

use App\Support\AdminAccess;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (AdminAccess::hasActiveAdminRole($user)) {
            return $next($request);
        }

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->isActive()) {
            abort(403);
        }

        if ($user) {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        if ($request->expectsJson()) {
            abort(403);
        }

        return redirect()
            ->route('login')
            ->with('status', 'Your staff account is inactive.');
    }
}
