<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->secure()) {
            // Comente esta linha se precisar desativar o HTTPS localmente.
            // return redirect()->secure($request->getRequestUri());
        }

        return $next($request);
    }
}
