<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyAdminToken
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->route('token') !== config('app.admin_route_token')) {
            abort(404); // atau redirect ke login
        }

        return $next($request);
    }
}

