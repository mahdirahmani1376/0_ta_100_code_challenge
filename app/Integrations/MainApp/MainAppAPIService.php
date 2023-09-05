<?php
// TODO rethink exception handling
namespace App\Integrations\MainApp;

use App\Exceptions\SystemException\MainAppInternalAPIException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class MainAppAPIService extends BaseMainAppAPIService
{
    /**
     * @throws MainAppInternalAPIException
     */
    public static function getConfig(string $key)
    {
        $url = '/api/internal/finance/config';
        $param = ['key' => $key];

        try {
            $response = self::makeRequest('get', $url, $param);

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

    /**
     * @throws MainAppInternalAPIException
     */
    public static function storeConfig(string $key, string $value)
    {
        $url = '/api/internal/finance/config';
        $param = [
            'key' => $key,
            'value' => $value,
        ];

        try {
            $response = self::makeRequest('post', $url, $param);

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
    public static function getClients(array $clientIds): array
    {
        $url = '/api/internal/finance/client';
        $param = ['client_ids' => $clientIds];

        try {
            $response = self::makeRequest('get', $url, $param);

            if ($response->status() == Response::HTTP_OK) {
                $clients = [];
                foreach ($response->json('data') as $item) {
                    $client = new \stdClass();
                    foreach ($item as $key => $value) {
                        $client->{$key} = $value;
                    }
                    $clients[] = $client;
                }

                return $clients;
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

    public static function updateClient(int $clientId, array $data)
    {
        $url = '/api/internal/finance/client/' . $clientId;

        try {
            $response = self::makeRequest('put', $url, $data);

            if ($response->status() == Response::HTTP_OK) {
                return $response->json('data');
            }

            Log::error('MainApp internal api error', [
                'url' => $url,
                'param' => json_encode($data),
                'response' => $response->body(),
            ]);

            throw MainAppInternalAPIException::make($url, json_encode($data));
        } catch (\Exception $exception) {
            if ($exception instanceof MainAppInternalAPIException) {
                throw $exception;
            }

            throw MainAppInternalAPIException::make($url, json_encode($data));
        }
    }
}
