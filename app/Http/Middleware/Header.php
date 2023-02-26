<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;


class Header
{
    public function handle(Request $request, Closure $next): JsonResponse|RedirectResponse
    {
        $response = $next($request);
        $response->headers->set('Authorization', 'Bearer '.$request->bearerToken());

        return $response;
    }
}
