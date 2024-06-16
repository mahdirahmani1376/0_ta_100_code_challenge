<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HealthCheckAction
{
    public function __invoke()
    {
        try {
            DB::getPdo();
            Cache::set('TEST_REDIS_CONNECTION', 1);
            Cache::get('TEST_REDIS_CONNECTION');
            Cache::forget('TEST_REDIS_CONNECTION');
            return response()->json(null);
        } catch (\Throwable $exception) {
            return response()->json(null, 503);
        }
    }
}
