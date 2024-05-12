<?php

namespace App\Providers;

use App\Models\BankGateway;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/home';

    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(1000)->by($request->user()?->id ?: $request->ip());
        });

        $this->bindRoutes();
    }

    public function bindRoutes(): void
    {
	Route::prefix('api')
            ->middleware('api')
	    ->namespace('App\Http\Controllers')
	    ->group(base_path('routes/api.php'));

    }

}
