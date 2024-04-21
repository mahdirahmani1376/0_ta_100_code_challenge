<?php
// TODO fix the following issues in this file
// 1- error handling strategy
// 2- refactor the redundant codes and encapsulate re-usable methods

namespace App\Integrations\MainApp;

use App\Exceptions\SystemException\MainAppInternalAPIException;
use App\Integrations\Rahkaran\ValueObjects\Client;
use App\Models\Invoice;
use Exception;
use Illuminate\Http\Response;

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
        } catch (Exception $exception) {
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
            'key'   => $key,
            'value' => $value,
        ];

        try {
            $response = self::makeRequest('post', $url, $param);

            if ($response->successful()) {
                return $response->json('data');
            }
        } catch (Exception $exception) {
            throw MainAppInternalAPIException::make($url, json_encode($param));
        }
    }

    /**
     * @throws MainAppInternalAPIException
     */
    public static function getClients(int|array $clientIds, bool $noRahkaranId = false): array
    {// todo use profile_id
        if (!is_array($clientIds)) {
            $clientIds = [$clientIds];
        }
        $url = '/api/internal/finance/client';
        $param = [
            'profile_ids' => $clientIds,
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

            if ($response->status() == Response::HTTP_NOT_FOUND) {
                return [];
            }

            throw MainAppInternalAPIException::make($url, json_encode($param));
        } catch (Exception $exception) {
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
        } catch (Exception $exception) {
            throw MainAppInternalAPIException::make($url, json_encode($data));
        }
    }

    public static function getProductOrDomain(string $type, int $invoiceableId): array
    {
        $url = '/api/internal/finance/product-domain';
        $data = [
            'type'   => $type,
            'rel_id' => $invoiceableId,
        ];

        try {
            $response = self::makeRequest('get', $url, $data);

            if ($response->successful()) {
                return $response->json('data');
            }

            throw MainAppInternalAPIException::make($url, json_encode($data));
        } catch (Exception $exception) {
            throw MainAppInternalAPIException::make($url, json_encode($data));
        }
    }

    public static function invoicePostProcess(Invoice $invoice)
    {
        $url = '/api/internal/finance/invoice/post-process';
        $data = [
            'id'        => $invoice->id,
            'client_id' => $invoice->profile->client_id,
            'total'     => $invoice->total,
            'items'     => $invoice->items,
        ];

        try {
            $response = self::makeRequest('post', $url, $data);

            if ($response->status() == Response::HTTP_OK) {
                return;
            }
        } catch (Exception $exception) {
            throw MainAppInternalAPIException::make($url, json_encode($data));
        }
    }

    public static function sendInvoiceReminder(array $payload, $channel = 'email')
    {
        $url = '/api/internal/finance/invoice/reminder';

        $data = [
            'channel'   => $channel,
            'subject'   => $payload['subject'] ?? null,
            'reminders' => $payload['reminders'],
        ];

        try {
            self::makeRequest('post', $url, $data);
        } catch (Exception $exception) {
            throw MainAppInternalAPIException::make($url, json_encode($data));
        }
    }

    public static function sendInvoiceCreateEmail(int $clientId, string $subject, string $message)
    {
        $url = '/api/internal/finance/invoice/create-email';
        $data = [
            'client_id' => $clientId,
            'subject'   => $subject,
            'message'   => $message,
        ];

        try {
            self::makeRequest('post', $url, $data);
        } catch (Exception $exception) {
            throw MainAppInternalAPIException::make($url, json_encode($data));
        }
    }

    public function adminListProducts($array)
    {
        $url = '/api/internal/finance/products';

        try {
            return self::makeRequest('get', $url, $array);
        } catch (Exception $exception) {
            throw MainAppInternalAPIException::make($url, json_encode($array));
        }
    }

    public static function getProductsById($productIds)
    {
        $url = '/api/internal/finance/products';

        $data = [
            'profile_ids' => [
                $productIds
            ]
        ];

        try {
            $response = self::makeRequest('get', $url, $data);
            if ($response->successful()) {
                return $response->json('data');
            }

            throw MainAppInternalAPIException::make($url, json_encode($data));
        } catch (Exception $exception) {
            throw MainAppInternalAPIException::make($url, json_encode($data));
        }
    }

    public static function getDomainsById(array $profileIds)
    {
        $url = '/api/internal/finance/domains';

        $data = [
            'profile_ids' => [
                $profileIds
            ]
        ];

        try {
            $response = self::makeRequest('get', $url, $data);

            if ($response->successful()) {
                return $response->json('data');
            }

        } catch (Exception $exception) {
            throw MainAppInternalAPIException::make($url, json_encode($data));
        }
    }

    public static function recalculateDomainServicePrice($domainId)
    {
        $url = "/api/internal/finance/$domainId/recalculate-domain-service";

        $response = self::makeRequest('get', $url);

        return $response->json();

    }

    public static function recalculateProductServicePrice($serviceId)
    {
        $url = "/api/internal/finance/$serviceId/recalculate-product-service";

        $response = self::makeRequest('get', $url);

        return $response->json();

    }

}
