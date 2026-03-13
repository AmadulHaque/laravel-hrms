<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShareGlobalSettings
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        Inertia::share([
            'globalSettings' => function () {
                return settings(); // Use our helper function
            }
        ]);

        return $next($request);
    }
}