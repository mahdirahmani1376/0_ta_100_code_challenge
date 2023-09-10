<?php
// TODO rethink exception handling
namespace App\Integrations\MainApp;

use App\Exceptions\SystemException\MainAppInternalAPIException;
use App\Integrations\Rahkaran\ValueObjects\Client;
use App\Integrations\Rahkaran\ValueObjects\Product;
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
    public static function getClients(int|array $clientIds, bool $noRahkaranId = false): array
    {
        if (!is_array($clientIds)) {
            $clientIds = [$clientIds];
        }
        $url = '/api/internal/finance/client';
        $param = [
            'client_ids' => $clientIds,
            'no_rahkaran' => $noRahkaranId,
        ];

        try {
            $response = self::makeRequest('get', $url, $param);

            if ($response->status() == Response::HTTP_OK) {
                $clients = [];
                foreach ($response->json('data') as $items) {
                    $client = new Client();
                    foreach ($items as $key => $value) {
                        $client->$key = $value;
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

    public static function getProduct(int $invoiceableId): ?Product
    {
        $url = '/api/internal/finance/product';
        $data = ['rel_id' => $invoiceableId,];

        try {
            $response = self::makeRequest('get', $url, $data);

            if ($response->status() == Response::HTTP_OK) {
                $product = new Product();
                foreach ($response->json('data') as $key => $value) {
                    $product->$key = $value;
                }

                return $product;
            }
            if ($response->status() == Response::HTTP_NOT_FOUND) {
                return null;
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
