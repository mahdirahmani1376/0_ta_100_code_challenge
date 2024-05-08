<?php

namespace App\Integrations\BankGateway;

use App\Exceptions\Http\BadRequestException;
use App\Exceptions\SystemException\BazaarPayAPIException;
use App\Integrations\BankGateway\Interface\BankGatewayInterface;
use App\Models\BankGateway;
use App\Models\DirectPayment;
use App\Models\Transaction;
use App\Services\BankGateway\DirectPayment\FindDirectPaymentByProfileIdService;
use App\Services\BankGateway\DirectPayment\UpdateDirectPaymentService;
use App\Services\Transaction\UpdateTransactionService;
use Illuminate\Support\Facades\Http;

class BazaarPay extends BaseBankGateway implements Interface\BankGatewayInterface
{
    const TRACE_CONTRACT_STATUS_ACTIVE = 'active';

    private UpdateTransactionService $updateTransactionService;
    private FindDirectPaymentByProfileIdService $findDirectPaymentByProfileIdService;
    private UpdateDirectPaymentService $updateDirectPaymentService;

    public function __construct(private readonly BankGateway $bankGateway)
    {
        $this->updateTransactionService = app(UpdateTransactionService::class);
        $this->findDirectPaymentByProfileIdService = app(FindDirectPaymentByProfileIdService::class);
        $this->updateDirectPaymentService = app(UpdateDirectPaymentService::class);
    }

    public static function make(BankGateway $bankGateway): BankGatewayInterface
    {
        return new static($bankGateway);
    }

    public function getRedirectUrlToGateway(Transaction $transaction, string $callbackUrl): string
    {
        return $callbackUrl;
    }

    public function callbackFromGateway(Transaction $transaction, array $data): Transaction
    {
        //TODO add 'provider' parameter to distinguish between different providers
        $directPayment = ($this->findDirectPaymentByProfileIdService)($transaction->profile_id);

        // check contract_token validity ( trace endpoint )
        $directPayment = self::validateContractToken($directPayment, $transaction);

        // create checkout_token
        $checkoutToken = self::createCheckoutToken($directPayment, $transaction);
        $transaction = ($this->updateTransactionService)($transaction, [
            'tracking_code' => $checkoutToken,
        ]);

        // direct_payment endpoint with contract_token + checkout_token , no need to call commit endpoint
        $response = Http::withHeader('Content-Type', 'application/json')
            ->withToken($this->bankGateway->config['authorization_token'])
            ->get($this->bankGateway->config['direct_pay_url'], [
                'contract_token' => $directPayment->config['contract_token'],
                'checkout_token' => $checkoutToken,
            ]);

        if (!$response->successful()) {
            ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
            throw BazaarPayAPIException::make($response->body(), $response->status(), json_encode([
                'route'             => $this->bankGateway->config['direct_pay_url'],
                'direct_payment_id' => $directPayment->id,
                'transaction_id'    => $transaction->id,
            ]));
        }

        return ($this->updateTransactionService)($transaction, [
            'status' => Transaction::STATUS_SUCCESS,
        ]);
    }

    private function validateContractToken(DirectPayment $directPayment, Transaction $transaction): DirectPayment
    {
        $response = Http::withHeader('Content-Type', 'application/json')
            ->withToken($this->bankGateway->config['authorization_token'])
            ->get($this->bankGateway->config['trace_url'], [
                'contract_token' => $directPayment->config['contract_token'],
            ]);

        if (!$response->successful()) {
            ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
            throw BazaarPayAPIException::make($response->body(), $response->status(), json_encode([
                'route'             => $this->bankGateway->config['trace_url'],
                'direct_payment_id' => $directPayment->id,
            ]));
        }
        if ($response->json('status') != self::TRACE_CONTRACT_STATUS_ACTIVE) {
            ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
            throw new BadRequestException(__('finance.direct_payment.bazaar_pay.contract_token_not_active'));
        }
        if ($directPayment->status != DirectPayment::STATUS_ACTIVE) {
            ($this->updateDirectPaymentService)($directPayment, [
                'status' => DirectPayment::STATUS_ACTIVE,
            ]);
        }

        return $directPayment;
    }

    private function createCheckoutToken(DirectPayment $directPayment, Transaction $transaction): string
    {
        $response = Http::withHeader('Content-Type', 'application/json')
            ->withToken($this->bankGateway->config['authorization_token'])
            ->post($this->bankGateway->config['init_checkout_url'], [
                'amount'       => $transaction->amount,
                'destination'  => 'HostIran',
                'service_name' => __('finance.direct_payment.bazaar_pay.service_name', ['invoice_id' => $transaction->invoice_id]),
            ]);

        if (!$response->successful()) {
            ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
            throw BazaarPayAPIException::make($response->body(), $response->status(), json_encode([
                'route'             => $this->bankGateway->config['init_checkout_url'],
                'direct_payment_id' => $directPayment->id,
                'transaction_id'    => $transaction->id,
            ]));
        }

        return $response->json('checkout_token');
    }

    public function initContract(): string
    {
        $response = Http::withHeader('Content-Type', 'application/json')
            ->withToken($this->bankGateway->config['authorization_token'])
            ->post($this->bankGateway->config['init_contract_url'], [
                'type'         => 'direct_debit',
                'period'       => 'yearly', //todo check where to get these values, maybe read it from mainapp's config, maybe .env ?
                'amount_limit' => 1000000000, //todo check where to get these values, maybe read it from mainapp's config, maybe .env ?
            ]);

        if (!$response->successful()) {
            throw BazaarPayAPIException::make($response->body(), $response->status(), json_encode([
                'route' => $this->bankGateway->config['init_contract_url'],
            ]));
        }

        return $response->json('contract_token');
    }
}
