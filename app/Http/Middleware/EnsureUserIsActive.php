<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Guests are handled by Laravel's auth middleware.
        if (! $user) {
            return $next($request);
        }

        // Allow users without the new column only if your migration
        // has not yet been deployed; production should have is_active.
        if (! $user->is_active) {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Your account is inactive. Please contact an administrator.',
                ]);
        }

        return $next($request);
    }
}
