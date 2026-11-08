<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Middleware pengecek guard aktif. Daftarkan sebagai alias 'guard:web', 'guard:guru', 'guard:siswa'
class RedirectIfNotGuard
{
    public function handle(Request $request, Closure $next, string $guard)
    {
        if (! Auth::guard($guard)->check()) {
            $routeName = match ($guard) {
                'guru' => 'guru.login',
                'siswa' => 'siswa.login',
                default => 'admin.login',
            };
            return redirect()->route($routeName);
        }

        return $next($request);
    }
}
