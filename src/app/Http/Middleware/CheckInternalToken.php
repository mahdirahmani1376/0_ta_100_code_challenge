<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInternalToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = config('services.main_app.token');

        if ($request->bearerToken() != $token || empty($token)) {
            throw new AuthenticationException();
        }

        return $next($request);
    }
}
