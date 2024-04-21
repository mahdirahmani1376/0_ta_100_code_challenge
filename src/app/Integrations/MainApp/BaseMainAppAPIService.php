<?php

namespace App\Integrations\MainApp;

use App\Models\SystemLog;
use App\Services\LogService;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

abstract class BaseMainAppAPIService
{
    protected static function makeRequest(string $method, string $path, array $body = [], array $header = [], bool $log = true): Response
    {
        $url = config('services.main_app.base_url', 'main-application_hostiran_app_1') . $path;
        $headers = [
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . config('services.main_app.token'),
            ...$header
        ];

        $response = Http::withHeaders($headers)->$method($url, $body);

        if ($log) {
            LogService::store(SystemLog::make(), [
                'method'      => 'get',
                'endpoint'    => self::setEndpoint(),
                'request_url' => $url,
                'request_body' => $body,
                'request_header' => $headers,
                'provider'    => SystemLog::PROVIDER_OUTGOING,
                'response_header' => $response->headers(),
                'response_body'   => $response->json(),
                'response_status' => $response->status()
            ]);
        }

        return $response;
    }

    private static function setEndpoint(): string
    {
        return match (self::class) {
            MainAppAPIService::class => 'main_application',
            default => 'unknown'
        };
    }
}
