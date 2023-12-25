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

        $this->bindRouteParameter();
    }

    public function bindRoutes(): void
    {
        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->namespace('App\Http\Controllers')
                ->group(base_path('routes/api.php'));
        });
    }

    public function bindRouteParameter(): void
    {
        Route::bind('publicGatewayName', function ($gatewayName) {
            return BankGateway::query()
                ->where('name', Str::lower($gatewayName))
                ->where('status', BankGateway::STATUS_ACTIVE)
                ->firstOrFail();
        });
    }
}
