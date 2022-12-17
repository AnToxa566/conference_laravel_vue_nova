<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use App\UserConsts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Admin
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
        if (auth('sanctum')->user()->type !== UserConsts::ADMIN) {
            abort(403);
        }

        return $next($request);
    }
}
