<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;


class IsAnnouncer
{
    public function handle(Request $request, Closure $next): JsonResponse|RedirectResponse
    {
        if ($request->user()->type !== User::ANNOUNCER) {
            abort(403);
        }

        return $next($request);
    }
}
