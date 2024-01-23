<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SetAppLanguage
{
    const DEFAULT_LANG = 'fa';
    const LANGUAGES = [
        'en',
        'fa',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $acceptLanguageHeader = Str::lower($request->header('accept-language'));
        $langQueryString = Str::lower($request->get('lang'));

        if (in_array($acceptLanguageHeader, self::LANGUAGES)) {
            App::setLocale($acceptLanguageHeader);
        } else if (in_array($langQueryString, self::LANGUAGES)) {
            App::setLocale($langQueryString);
        } else {
            App::setLocale(self::DEFAULT_LANG);
        }

        return $next($request);
    }
}
