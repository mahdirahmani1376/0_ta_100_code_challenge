<?php

namespace App\Providers;

use App\Models\Invoice;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        //
    ];

    public function boot(): void
    {
        Gate::define('access-invoice', function (Invoice $invoice) {
            dd(request('client_id'));
            return $invoice->client_id == request('client_id');
        });
    }
}
