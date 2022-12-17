<?php

namespace App\Http\Middleware;

use Closure;
use App\UserConsts;
use Illuminate\Http\Request;

class IsAnnouncer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth('sanctum')->user()->type !== UserConsts::ANNOUNCER) {
            abort(403);
        }

        return $next($request);
    }
}
