<?php

namespace App\Integrations\MainApp;

use App\Exceptions\SystemException\MainAppInternalAPIException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MainAppService
{
    /**
     * @throws MainAppInternalAPIException
     */
    public static function getConfig(string $key)
    {
        $url = config('services.main_app.base_url') . config('services.main_app.get_config_url');
        $param = ['key' => $key];

        try {
            $response = Http::withHeader('Accept', 'application/json')
                ->withHeader('Content-Type', 'application/json')
                ->withToken('token')
                ->get($url, $param);

            if ($response->status() == Response::HTTP_OK) {
                return $response->json('data');
            }

            Log::error('MainApp internal api error', [
                'url' => $url,
                'param' => json_encode($param),
                'response' => $response->body(),
            ]);

            throw MainAppInternalAPIException::make($url, json_encode($param));
        } catch (\Exception $exception) {
            if ($exception instanceof MainAppInternalAPIException) {
                throw $exception;
            }

            throw MainAppInternalAPIException::make($url, json_encode($param));
        }
    }
    public static function storeConfig(string $key, string $value)
    {
        $url = config('services.main_app.base_url') . config('services.main_app.create_config_url');
        $param = [
            'key' => $key,
            'value' => $value,
        ];

        try {
            $response = Http::withHeader('Accept', 'application/json')
                ->withHeader('Content-Type', 'application/json')
                ->withToken('token')
                ->post($url, $param);

            if ($response->successful()) {
                return $response->json('data');
            }

            Log::error('MainApp internal api error', [
                'url' => $url,
                'param' => json_encode($param),
                'response' => $response->body(),
            ]);

            throw MainAppInternalAPIException::make($url, json_encode($param));
        } catch (\Exception $exception) {
            if ($exception instanceof MainAppInternalAPIException) {
                throw $exception;
            }

            throw MainAppInternalAPIException::make($url, json_encode($param));
        }
    }

    /**
     * @throws MainAppInternalAPIException
     */
    public static function getClients(array $clientIds)
    {
        $url = config('services.main_app.base_url') . config('services.main_app.get_clients_url');
        $param = ['client_ids' => $clientIds];

        try {
            $response = Http::withHeader('Accept', 'application/json')
                ->withHeader('Content-Type', 'application/json')
                ->withToken('token')
                ->get($url, $param);

            if ($response->status() == Response::HTTP_OK) {
                return $response->json('data');
            }

            Log::error('MainApp internal api error', [
                'url' => $url,
                'param' => json_encode($param),
                'response' => $response->body(),
            ]);

            throw MainAppInternalAPIException::make($url, json_encode($param));
        } catch (\Exception $exception) {
            if ($exception instanceof MainAppInternalAPIException) {
                throw $exception;
            }

            throw MainAppInternalAPIException::make($url, json_encode($param));
        }
    }
}
