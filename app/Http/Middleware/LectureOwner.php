<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;


class LectureOwner
{
    public function handle(Request $request, Closure $next): JsonResponse|RedirectResponse
    {
        if ($request->user()->id !== $request->input('user_id')) {
            abort(403);
        }

        return $next($request);
    }
}
