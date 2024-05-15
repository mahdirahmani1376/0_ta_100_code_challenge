<?php

namespace App\Integrations\MainApp;

use App\Jobs\UpdateSystemLog;
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

        if ($log) {
            $systemLog = LogService::store(SystemLog::make(), [
                'method'         => $method,
                'endpoint'       => SystemLog::ENDPOINT_MAIN_APP,
                'request_url'    => $url,
                'request_body'   => $body,
                'request_header' => $headers,
                'provider'       => SystemLog::PROVIDER_OUTGOING,
            ]);
        }

        $response = Http::withHeaders($headers)->$method($url, $body);

        if (isset($systemLog)) {
            $customResponse = [
                'header' => $response->headers(),
                'body'   => $response->json(),
                'status' => $response->status()
            ];
            UpdateSystemLog::dispatch($systemLog,$customResponse)->onQueue(UpdateSystemLog::DEFAULT_QUEUE);
        }

        return $response;
    }
}
