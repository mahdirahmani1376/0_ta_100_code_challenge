<?php

namespace App\Integrations\MainApp;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

abstract class BaseMainAppAPIService
{
    protected static function makeRequest(string $method, string $path, array $body = [], array $header = []): Response
    {
        $url = config('services.main_app.base_url', 'main-application_hostiran_app_1') . $path;
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . config('services.main_app.token'),
            ...$header
        ];

        $response = Http::withHeaders($headers)->$method($url, $body);

        return $response;
    }
}
