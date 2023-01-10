<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Routing\Redirector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    protected function redirectTo($request): Redirector|RedirectResponse
    {
        if (! $request->expectsJson()) {
            return redirect('/login');
        }
    }
}
