<?php

namespace App\Services;

use App\Jobs\UpdateSystemLog;
use App\Models\AbstractBaseLog;
use App\Models\AdminLog;
use App\Models\SystemLog;
use App\ValueObjects\Queue;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Throwable;

class AsanPardakhtRefundService
{

    private Client $client;

    private ?AbstractBaseLog $sysLog;

    private string $authUrl;

    private string $url;

    private string $client_id;

    private string $client_secret;

    private string $token_type;

    private string $token;

    public function __construct(Client $client)
    {

        $this->client = $client;

        $this->authUrl       = config('services.asan_pardakht_refund_auth_url');
        $this->url           = config('services.asan_pardakht_refund_api_url');
        $this->client_id     = config('services.asan_pardakht_refund_client_id');
        $this->client_secret = config('services.asan_pardakht_refund_secret');
    }


    public function getWallet()
    {
        $this->getToken();

        return $this->sendRequest(
            'GET',
            $this->url . '/rms/v1/clients/credit/balance',
            [],
            ['Authorization' => "$this->token_type $this->token"]
        );
    }

    private function getToken()
    {
        $response = $this->sendRequest(
            'POST',
            $this->authUrl . '/connect/token',
            [
                'client_id'     => $this->client_id,
                'client_secret' => $this->client_secret,
                'grant_type'    => 'client_credentials'
	],
	['Content-Type' => 'application/x-www-form-urlencoded']
        );

        $this->token = $response['access_token'];
        $this->token_type = $response['token_type'];
    }

    private function sendRequest($method, $url, $body = [], $headers = ['Content-Type' => 'application/json'])
    {

        try {

            admin_log(AdminLog::PROVIDER_OUTGOING,$this->client,$this->client,validatedData: $body,adminId: auth()->id());

            $result = $this->client->$method($url, [
                'headers' => $headers,
                'form_params'    => $body
            ]);

            $this->updateResponseLog(
                $result->getBody(),
                $result->getHeaders(),
                $result->getStatusCode()
            );

            return json_decode((string)$result->getBody(), true);

        } catch (\Exception $e) {
	    $response = $e->getResponse();
            $response_body = $response->getBody()->getContents() ?? '';

	    $this->updateResponseLog(
                $response_body,
                $response->getHeaders(),
                $response->getStatusCode()
            );

            return $response_body;
        }
    }

    /**
     * @param string $body
     * @param array $headers
     * @param int $status
     */
    private function updateResponseLog(string $body, array $headers = [], int $status = 200)
    {
        try {
            $custom_response = [
                'header' => $headers,
                'body'   => $body,
                'status' => $status
            ];

            if ($this->sysLog instanceof AbstractBaseLog)
                dispatch(new UpdateSystemLog($this->sysLog, $custom_response))->onQueue(Queue::SYSTEM_LOG_QUEUE);
        } catch (Throwable $exception) {
            Log::error('AsanPardakht Refund Service', $exception->getTrace());
        }
    }

    public function refundByIban($iban, $amount, $inquiryCode, $mobileNumber = null)
    {
        $this->getToken();

        return $this->sendRequest(
            'POST',
            $this->url . '/rms/v1/refunds/iban',
            [
                'iban' => $iban,
                'amount' => $amount,
                'inquiryCode' => $inquiryCode,
                'purposeId' => 1,
                'mobileNumber' => $mobileNumber
            ],
            [
                'Authorization' => "$this->token_type $this->token",
		'Content-Type'  => "application/json"

            ]
        );
    }

    private function createRequestLog(string $method, string $url, array $request_params = [], array $headers = [], string $request_type = AbstractBaseLog::PROVIDER_OUTGOING)
    {
        $this->sysLog = LogService::store((new SystemLog()), [
            'method' => $method,
            'endpoint' => 'asanpardakht_refund',
            'request_url' => $url,
            'request_body' => $request_params,
            'request_header' => $headers,
            'provider' => AbstractBaseLog::PROVIDER_OUTGOING,
        ]);
    }

}



