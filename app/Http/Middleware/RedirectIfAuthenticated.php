<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Logika baru: Arahkan berdasarkan guard
                if ($guard === 'anggota') {
                    // Jika anggota sudah login, arahkan ke kasir
                    return redirect()->route('anggota.kasir', ['anggota' => Auth::user()->id]);
                }

                // Jika admin sudah login, arahkan ke dashboard admin
                // Anda perlu membuat route untuk ini jika belum ada
                // return redirect()->route('admin.dashboard'); 

                // Redirect default jika tidak ada yang cocok
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
