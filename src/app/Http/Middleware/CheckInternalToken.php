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

        change_log()->init($request);

        if ($request->bearerToken() != $token || empty($token)) {
            throw new AuthenticationException();
        }

        $response = change_log()->setResponse($next($request));

        change_log()->dispatch();

        return $response;
    }
}
