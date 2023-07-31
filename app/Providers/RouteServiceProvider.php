<?php

namespace App\Providers;

use App\Models\ClientBankAccount;
use App\Models\Invoice;
use App\Models\OfflineTransaction;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/home';

    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->bindRoutes();

        $this->bindRouteParameter();
    }

    public function bindRoutes(): void
    {
        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api/profile')
                ->namespace('App\Http\Controllers\Profile')
                ->group(base_path('routes/profile-api.php'));

            Route::middleware('api')
                ->prefix('api/public')
                ->namespace('App\Http\Controllers\Public')
                ->group(base_path('routes/public-api.php'));

            Route::middleware('api')
                ->prefix('api/admin')
                ->namespace('App\Http\Controllers\Admin')
                ->group(base_path('routes/admin-api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    public function bindRouteParameter(): void
    {
        Route::bind('profileInvoice', function ($profileInvoiceId) {
            return Invoice::query()
                ->where('client_id', request('client_id'))
                ->where('id', $profileInvoiceId)
                ->firstOrFail();
        });
        Route::bind('profileOfflineTransaction', function ($profileOfflineTransaction) {
            return OfflineTransaction::query()
                ->where('client_id', request('client_id'))
                ->where('id', $profileOfflineTransaction)
                ->firstOrFail();
        });
        Route::bind('profileClientBankAccount', function ($clientBankAccount) {
            return ClientBankAccount::query()
                ->where('client_id', request('client_id'))
                ->where('id', $clientBankAccount)
                ->firstOrFail();
        });
    }
}
