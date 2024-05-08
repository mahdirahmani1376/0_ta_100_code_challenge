<?php

namespace App\Integrations\Rahkaran;

use App\Exceptions\Http\BadRequestException;
use App\Exceptions\Http\FatalErrorException;
use App\Exceptions\Repository\ModelNotFoundException;
use App\Exceptions\SystemException\MainAppInternalAPIException;
use App\Integrations\MainApp\MainAppAPIService;
use App\Integrations\MainApp\MainAppConfig;
use App\Integrations\Rahkaran\ValueObjects\Client;
use App\Integrations\Rahkaran\ValueObjects\DlObject;
use App\Integrations\Rahkaran\ValueObjects\Party as RahkaranParty;
use App\Integrations\Rahkaran\ValueObjects\PartyAddress;
use App\Integrations\Rahkaran\ValueObjects\Payment;
use App\Integrations\Rahkaran\ValueObjects\PaymentDeposit;
use App\Integrations\Rahkaran\ValueObjects\Product;
use App\Integrations\Rahkaran\ValueObjects\Receipt;
use App\Integrations\Rahkaran\ValueObjects\ReceiptDeposit;
use App\Integrations\Rahkaran\ValueObjects\Voucher;
use App\Integrations\Rahkaran\ValueObjects\VoucherItem;
use App\Jobs\UpdateSystemLog;
use App\Models\AbstractBaseLog;
use App\Models\ClientCashout;
use App\Models\CreditTransaction;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Profile;
use App\Models\SystemLog;
use App\Models\Transaction;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;
use App\Services\BankGateway\FindBankGatewayByNameService;
use App\Services\Invoice\AssignInvoiceNumberService;
use App\Services\LogService;
use App\ValueObjects\Queue;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\RSA\PublicKey;
use phpseclib3\Math\BigInteger;
use Throwable;

class RahkaranService
{
    private HttpClient $client;
    private string $baseUrl;
    private string $sessionId = '';
    private array $rsaParams = [];
    private array $regions = [];

    public function __construct(
        private readonly TransactionRepositoryInterface $transactionRepository,
        private readonly InvoiceRepositoryInterface     $invoiceRepository,
        private readonly AssignInvoiceNumberService     $assignInvoiceNumberService,
        private readonly FindBankGatewayByNameService   $findBankGatewayByNameService,
        public RahkaranConfig                           $config
    )
    {

        if ($this->isTestMode()) {
            return;
        }

        $this->client = new HttpClient();
        $this->baseUrl = config('rahkaran.rahkaran_baseurl');
        $this->initSession();
        if ($this->login()) {
            throw new FatalErrorException(trans('rahkaran.error.CONNECTION_ERROR'));
        }
    }


    // ########## ########## ##########
    // ####### Public Methods #######
    // ########## ########## ##########
    /**
     * Returns Rahkaran test mode
     *
     * @return bool
     */
    public function isTestMode(): bool
    {
        return (bool)$this->config->testMode;
    }

    /**
     * Gets Rahkaran Configuration object
     *
     * @return RahkaranConfig
     */
    public function getConfig(): RahkaranConfig
    {
        return $this->config;
    }

    /**
     * Creates party by given client model
     *
     * @param Client $client
     * @param array $ignored_fields
     * @return RahkaranParty|null
     * @throws MainAppInternalAPIException
     */
    public function createClientParty(Client $client, array $ignored_fields = []): ?RahkaranParty
    {
        if (!$this->isTestMode() && $client->rahkaran_id) {
            return $this->getClientParty($client);
        }

        $client_party = $this->mapClientParty($client, new RahkaranParty(), $ignored_fields);

        if ($this->isTestMode()) {
            return $client_party;
        }

        $client_party = $this->createParty($client_party);

        $profile = Profile::where('id', $client->finance_profile_id)->first();
        $profile->update(['rahkaran_id' => $client_party->ID]);

        $this->getClientDl($client);

        return $client_party;
    }

    /**
     * Gets dl by given client model
     *
     * @param Client $client
     * @return DlObject|mixed|null
     */
    public function getClientDl(Client $client): mixed
    {
        $party_dl_code = $this->getClientDlPartyCode($client);

        $party_dl = $this->getDl($party_dl_code);

        if (!$party_dl) {
            return $this->generateClientDl($client);
        }

        return $party_dl;
    }

    /**
     * Updates client party by the client's data
     *
     * @param Client $client
     * @return RahkaranParty|null
     * @throws MainAppInternalAPIException
     */
    public function updateClientParty(Client $client): ?RahkaranParty
    {
        $client_party = $this->mapClientParty($client, $this->getClientParty($client));

        if ($this->isTestMode()) {
            return $client_party;
        }

        return $this->updateParty($client_party);
    }

    /**
     * Gets party by given client model
     *
     * @param Client $client
     * @return RahkaranParty|null
     * @throws MainAppInternalAPIException
     */
    public function getClientParty(Client $client): ?RahkaranParty
    {
        if (!$client->rahkaran_id) {
            try {
                return $this->createClientParty($client);
            } catch (Throwable $exception) {
                try {
                    return $this->createClientParty($client, ['address']);
                } catch (Throwable $exception) {
                    return $this->createClientParty($client, ['address', 'national_code']);
                }
            }
        }

        return $this->getPartyById($client->rahkaran_id);
    }

    /**
     * Creates receipt by given transaction
     *
     * @param Transaction $transaction
     * @return Transaction|Model
     * @throws ModelNotFoundException|MainAppInternalAPIException
     */
    public function createTransaction(Transaction $transaction): Transaction|Model
    {
        /** @var Client $client */
        $client = MainAppAPIService::getClients($transaction->invoice->profile->client_id)[0];

        if (!$transaction->invoice || !$transaction->invoice->client = $client) {
            throw new ModelNotFoundException('rahkaran');
        }

        if ($transaction->rahkaran_id || $transaction->payment_method == Transaction::PAYMENT_METHOD_CREDIT) {
            return $transaction;
        }

        if ($this->isRoundingTransaction($transaction)) {
            return $transaction;
        }

        if ($this->isBarterTransaction($transaction)) {
            return $transaction;
        }

        if ($transaction->status == Transaction::STATUS_REFUND && $transaction->payment_method == Transaction::PAYMENT_METHOD_CREDIT) {
            return $transaction;
        }

        $client_party = $this->getClientDl($client);

        $reference_id = $transaction->reference_id && $transaction->reference_id != '' && strlen(trim($transaction->reference_id)) > 0 ? $transaction->reference_id : 'Transaction-' . $transaction->id;

        $receipt = new Receipt();
        $receipt->BranchID = $this->config->bankBranchId;
        $receipt->IsApproved = true;
        $receipt->Number = $reference_id;
        $receipt->SecondNumber = $transaction->invoice_id;
        $receipt->CounterPartDLCode = $client_party->Code;
        $receipt->Date = $transaction->created_at;
        $receipt->ApproveDate = $transaction->created_at;
        $receipt->TotalOperationalCurrencyAmount = $transaction->amount;

        $receipt_deposit = new ReceiptDeposit();

        $receipt_deposit->Amount = round($transaction->amount);

        $receipt_deposit->BankAccountID = $this->getBankAccountId($transaction);
        $receipt_deposit->AccountingOperationID = $this->config->receiptAccountingOperationID;
        $receipt_deposit->CashFlowFactorID = $this->config->receiptCashFlowFactorID;

        $receipt_deposit->CounterPartDLCode = $client_party->Code;
        $receipt_deposit->Number = $reference_id;

        if ($receipt_deposit->Amount == 0) {
            return $this->transactionRepository->update($transaction, ['rahkaran_id' => 1], ['rahkaran_id']);
        }

        if ($transaction->invoice->is_credit) {
            $description = trans('rahkaran.receipt.ADD_CREDIT');
        } elseif ($transaction->amount < 10) {
            $description = $transaction->description ? $transaction->description : trans('rahkaran.receipt.ROUNDING_TRANSACTION_DESCRIPTION', [
                'code' => $reference_id
            ]);
        } else {
            $description = $transaction->description ? $transaction->description : trans('rahkaran.receipt.TRANSACTION_DESCRIPTION', [
                'code' => $reference_id
            ]);
        }

        $receipt->Description = $description;
        $receipt->Description_En = $description;

        $receipt_deposit->Description = $description;
        $receipt_deposit->Description_En = $description;
        $receipt_deposit->Date = $transaction->created_at;

        $receipt->ReceiptDeposits = [
            $receipt_deposit
        ];

        $result = $this->createReceipt($receipt);
        $transaction->rahkaran_id = $result['ID'];

        if ($this->isTestMode()) {
            return $transaction;
        }

        return $this->transactionRepository->update($transaction, ['rahkaran_id' => $result['ID']], ['rahkaran_id']);
    }

    /**
     * Creates receipt from given Receipt object
     *
     * @param Receipt $receipt
     * @return mixed
     */
    public function createReceipt(Receipt $receipt): array
    {
        if ($this->isTestMode()) {
            return [
                'ID' => 1
            ];
        }

        $result = $this->makeRequest(
            $this->baseUrl . '/ReceiptAndPayment/ReceiptManagement/Services/ReceiptManagementService.svc/RegisterReceipt',
            'post',
            $receipt,
            $this->getHeaders()
        );

        return $this->validateReceiptAndPayment($result);
    }

    /**
     * Creates payment from given Payment object
     *
     * @param Payment $payment
     * @return mixed
     */
    public function createPayment(Payment $payment): array
    {
        if ($this->isTestMode()) {
            return [
                'ID' => 1
            ];
        }

        $result = $this->makeRequest(
            $this->baseUrl . '/ReceiptAndPayment/PaymentManagement/Services/PaymentManagementService.svc/RegisterPayment',
            'post',
            $payment,
            $this->getHeaders()
        );

        return $this->validateReceiptAndPayment($result);
    }

    /**
     * @param Product $product
     * @return DlObject
     */
    public function getProductLevel4Dl(Product $product): DlObject
    {
        $dl_code = $this->getProductLevel4DlCode($product);

        $dl_object = $this->getDl(
            $dl_code
        );

        if (!$dl_object) {
            $this->createDl((string)$dl_code, $this->config->level4DlType, $product->productGroup->name, $product->productGroup->name);
            return $this->getProductLevel4Dl($product);
        } else {
            return $dl_object;
        }
    }

    /**
     * @param Product $product
     * @return DlObject
     */
    public function getProductLevel5Dl(Product $product): DlObject
    {
        $dl_code = $this->getProductLevel5DlCode($product);

        $dl_object = $this->getDl(
            $dl_code
        );

        if (!$dl_object) {
            $this->createDl((string)$dl_code, $this->config->level5DlType, $product->name, $product->description);
            return $this->getProductLevel5Dl($product);
        } else {
            return $dl_object;
        }
    }

    /**
     * @return DlObject
     */
    public function getDomainLevel4Dl(): DlObject
    {
        $dl_code = $this->getDomainLevel4DlCode();

        $dl_object = $this->getDl(
            $dl_code
        );

        if (!$dl_object) {
            $this->createDl((string)$dl_code, $this->config->level5DlType, trans('rahkaran.dl.DOMAIN_REGISTRATION'), trans('rahkaran.dl.DOMAIN_REGISTRATION'));
            return $this->getDomainLevel4Dl();
        } else {
            return $dl_object;
        }
    }

    /**
     * @param $tld
     * @return DlObject
     */
    public function getDomainLevel5Dl($tld): DlObject
    {
        $dl_code = $this->getDomainLevel5DlCode($tld);

        $dl_object = $this->getDl(
            $this->getDomainLevel5DlCode($tld)
        );

        $description = in_array($tld, ['ir', 'co.ir']) ? trans('rahkaran.dl.IR_DOMAIN') : trans('rahkaran.dl.INTERNATIONAL_DOMAIN');

        if (!$dl_object) {
            $this->createDl((string)$dl_code, $this->config->level5DlType, $description, $description);
            return $this->getDomainLevel5Dl($tld);
        } else {
            return $dl_object;
        }
    }

    /**
     * @param Invoice|null $invoice
     * @param null $voucherId
     * @return int[]|null
     */
    public function deleteVoucher(Invoice $invoice = null, $voucherId = null): ?array
    {
        if (!isset($invoice) && !isset($voucherId)) {
            return null;
        }

        if ($this->isTestMode()) {
            return [
                'ID' => 1
            ];
        }

        $result = null;
        if (isset($invoice)) {
            $result = $this->makeRequest($this->baseUrl . '/Financial/VoucherManagement/Services/VoucherService.svc/DeleteVoucher', 'post', [
                $invoice->rahkaran_id
            ], $this->getHeaders());

            $invoice->rahkaran_id = null;
            $this->invoiceRepository->update($invoice, ['rahkaran_id' => null], ['rahkaran_id']);
        }
        if (isset($voucherId)) {
            $result = $this->makeRequest($this->baseUrl . '/Financial/VoucherManagement/Services/VoucherService.svc/DeleteVoucher', 'post', [
                $voucherId
            ], $this->getHeaders());

        }


        return $result;
    }

    /**
     * @param Collection $cashouts
     * @param string|null $date
     * @return string|null
     */
    public function createZarinpalPaymentsFee(Collection $cashouts, ?string $date = null): ?string
    {
        $description = "سند کارمزد بازگشت وجه کاربران " . $date;

        $voucher = new Voucher();
        $voucher->AuxiliaryNumber = 0;
        $voucher->BranchRef = $this->config->voucherBranchRef;
        $voucher->Creator = $this->config->voucherCreatorId;
        $voucher->Date = $cashouts->first()->updated_at;
        $voucher->Description = $description;
        $voucher->Description_En = $description;
        $voucher->FiscalYearRef = MainAppConfig::get(MainAppConfig::RAHKARAN_FISCAL_YEAR_REF);
        $voucher->IsCurrencyBased = $this->config->voucherIsCurrencyBased;
        $voucher->IsExternal = true;
        $voucher->LedgerRef = $this->config->voucherLedgerRef;
        $voucher->State = $this->config->voucherState;
        $voucher->VoucherTypeRef = $this->config->voucherVoucherTypeRef;

        $amount = 0;
        /** @var ClientCashout $cashout */
        foreach ($cashouts as $cashout) {
            $fee = (int)(($cashout->amount * 0.001) + 5000);
            $amount += $fee;
            $voucher->addVoucherItem($this->getPaymentFeeTransactionVoucherItem($cashout, $fee));
        }

        $voucher->addVoucherItem($this->getPaymentFeeItem($amount));

        if ($this->isTestMode()) {
            return null;
        }

        $result = $this->createVoucher($voucher);
        return 'Created';
    }

    /**
     * @param $invoices
     * @param int|null $max_rounding_amount
     * @param string|null $date
     * @return string|null
     * @throws ModelNotFoundException
     */
    public function createBulkInvoice($invoices, ?int $max_rounding_amount = null, ?string $date = null): ?string
    {
        $processInvoices = [];

        /** @var Invoice $invoice */
        foreach ($invoices as $invoice) {
            if (!$invoice->is_credit &&
                !$invoice->is_mass_payment &&
                !$invoice->rahkaran_id &&
                in_array($invoice->status, [
                    Invoice::STATUS_PAID,
                    Invoice::STATUS_COLLECTIONS,
                    Invoice::STATUS_REFUNDED,
                ])) {
                ($this->assignInvoiceNumberService)($invoice);
                $invoice->refresh();
                $processInvoices[] = $invoice;

            }
        }

        $description = "سند فروش و بازگشت از فروش " . $date;

        $voucher = new Voucher();
        /** @var Invoice[] $invoices */
        $voucher->AuxiliaryNumber = $invoices[0]->invoiceNumber->invoice_number ?? $invoices[1]->invoiceNumber->invoice_number ?? $invoices[2]->invoiceNumber->invoice_number;
        $voucher->BranchRef = $this->config->voucherBranchRef;
        $voucher->Creator = $this->config->voucherCreatorId;
        $voucher->Date = $invoices[0]->paid_at ?? $invoices[0]->created_at;
        $voucher->Description = $description;
        $voucher->Description_En = $description;
        $voucher->FiscalYearRef = $this->config->fiscalYearRef;
        $voucher->IsCurrencyBased = $this->config->voucherIsCurrencyBased;
        $voucher->IsExternal = true;
        $voucher->LedgerRef = $this->config->voucherLedgerRef;
        $voucher->State = $this->config->voucherState;
        $voucher->VoucherTypeRef = $this->config->voucherVoucherTypeRef;

        foreach ($processInvoices as $invoice) {

            $is_refund = $invoice->status == Invoice::STATUS_REFUNDED;
            $invoice->client = MainAppAPIService::getClients($invoice->profile->client_id)[0];
            $client_dl_code = $this->getClientDl($invoice->client)->Code;

            $items = $invoice->items;

            $negativeAmount = $items->where('amount', '<', 0)->sum('amount');

            // Voucher items base on invoice items
            foreach ($items as $item) {

                if ($item->amount < 0) {
                    continue;
                }

                if ($negativeAmount && $negativeAmount != 0) {
                    if ($item->amount > abs($negativeAmount)) {
                        $item->amount = $item->amount - abs($negativeAmount);
                        $negativeAmount = 0;
                    } else {
                        $negativeAmount = -1 * (abs($negativeAmount) - $item->amount);
                        continue;
                    }
                }

                $voucher_item = $this->getAllTypesVoucherItem($item, $is_refund);

                $voucher_item_description = trim(str_replace(["\n", "\r", "\t"], [' '], $item->description));
                $voucher_item_description = mb_substr($voucher_item_description, 0, 511);

                $voucher_item->Description = $voucher_item_description;
                $voucher_item->Description_En = $voucher_item_description;

                /// ########
                /// Check and complete voucher item
                /// ########
                $voucher_item->PartyRef = $invoice->client->rahkaran_id;
                $voucher_item->TaxAmount = $this->getItemTax($item);
                $voucher_item->TollAmount = $this->getItemToll($item);
                $voucher_item->TaxStateType = 1;
                $voucher_item->PurchaseOrSale = 2;
                $voucher_item->ItemOrService = 2;
                $voucher_item->TransactionType = 1;

                $voucher->addVoucherItem($voucher_item);
            }

            // Tax Voucher Item
            $voucher->addVoucherItem($this->getNewTaxVoucherItem($invoice, $invoice->client->rahkaran_id));

            if ($invoice->status == Invoice::STATUS_PAID || $invoice->status == Invoice::STATUS_COLLECTIONS) {

                // Credit Transaction
                $transactions = $invoice->transactions()
                    ->whereIn('status', [
                        Transaction::STATUS_SUCCESS,
                        Transaction::STATUS_OPG_PAID,
                        Transaction::STATUS_IPG_PAID,
                    ])
                    ->where('payment_method', Transaction::PAYMENT_METHOD_CREDIT)
                    ->where('amount', '>=', 0)
                    ->get();
                foreach ($transactions as $transaction) {
                    $voucher->addVoucherItem(
                        $this->getCreditTransactionVoucherItem($client_dl_code, $transaction)
                    );
                }

                // Payment Transactions
                $transactions = $invoice->transactions()
                    ->where('reference_id', 'not like', 'ROUND-%')
                    ->where('payment_method', '<>', Transaction::PAYMENT_METHOD_CREDIT)
                    ->where('status', Transaction::STATUS_SUCCESS)
                    ->get();
                foreach ($transactions as $transaction) {

                    if ($transaction->amount < 0 || $this->isRoundingTransaction($transaction)) {
                        continue;
                    }

                    $voucher->addVoucherItem(
                        $this->getPaymentTransactionVoucherItem($client_dl_code, $transaction)
                    );
                }

                // Collection Negative voucher item to balance Voucher
                if ($invoice->status == Invoice::STATUS_COLLECTIONS) {
                    $collection_voucher_item = $this->getCollectionVoucherItem($client_dl_code, $invoice);
                    if ($collection_voucher_item) {
                        $voucher->addVoucherItem($collection_voucher_item);
                    }
                }

            } elseif ($invoice->status == Invoice::STATUS_REFUNDED) {
                if ($refund_voucher_item = $this->getRefundVoucherItem($client_dl_code, $invoice)) {
                    $voucher->addVoucherItem($refund_voucher_item);
                }

            }

            $item_balance = $voucher->VoucherItems->sum('Credit') - $voucher->VoucherItems->sum('Debit');
            $max_rounding_amount = $max_rounding_amount ?? 10;

            if (abs($item_balance) <= $max_rounding_amount) {
                if ($rounding_voucher_item = $this->getRoundingVoucherItem($item_balance)) {
                    $voucher->addVoucherItem($rounding_voucher_item);
                }
            }

            $item_balance = $voucher->VoucherItems->sum('Credit') - $voucher->VoucherItems->sum('Debit');

            if ($item_balance != 0) {
                var_dump($invoice->id . ' == ' . $item_balance);
                Log::warning("Rahkaran imbalanced invoice #{$invoice->id}", [
                    'balance' => $item_balance
                ]);
            }
        }

        if ($this->isTestMode()) {
            return null;
        }

        $result = $this->createVoucher($voucher);

        $rahkaran_id = $result['ID'];
        foreach ($processInvoices as $invoice) {
            $invoice->rahkaran_id = $rahkaran_id;
            $this->invoiceRepository->update($invoice, ['rahkaran_id' => $rahkaran_id], ['rahkaran_id']);
        }

        return "All Done.";
    }

    // ########## ########## ##########
    // ####### Private Methods #######
    // ########## ########## ##########
    /**
     * @return array|mixed
     */
    private function getSession()
    {
        return $this->makeRequest($this->baseUrl . '/Services/Framework/AuthenticationService.svc/session', 'get', [], $this->getHeaders());
    }

    /**
     * Init session
     */
    private function initSession()
    {
        $session = $this->getSession();
        if (isset($session['id'])) {
            $this->sessionId = $session['id'];
            $this->rsaParams = $session['rsa'];
        } else {
            throw new FatalErrorException();
        }
    }

    /**
     * Login to Rahkaran
     *
     * @return array|mixed
     */
    private function login(): ?array
    {
        $rsa = RSA::loadPublicKey([
                'e' => new BigInteger($this->rsaParams['E'], 16),
                'n' => new BigInteger($this->rsaParams['M'], 16)
            ]
        );

        /**
         * @var PublicKey $rsa
         */
        $rsa = $rsa->withPadding(RSA::ENCRYPTION_PKCS1 | RSA::SIGNATURE_PKCS1);
        $encrypted = $rsa->encrypt($this->sessionId . '**' . config('rahkaran.rahkaran_password'));

        $body = [
            'username'  => config('rahkaran.rahkaran_username'),
            'password'  => strtoupper(bin2hex($encrypted)),
            'sessionId' => $this->sessionId
        ];

        return $this->makeRequest($this->baseUrl . '/Framework/Services/AuthenticationService.svc/login', 'post', $body, $this->getHeaders());
    }

    /**
     * @return array|mixed
     */
    private function logout(): ?array
    {
        return $this->makeRequest($this->baseUrl . '/Framework/Services/AuthenticationService.svc/logout', 'post', [], $this->getHeaders());
    }

    /**
     * Gets Bank account id
     *
     * @param Transaction $transaction
     * @return int
     */
    private function getBankAccountId(Transaction $transaction): int
    {
        if ($transaction->amount < 10) {
            return $this->config->roundingBankId;
        }
        switch ($transaction->payment_method) {
            case 'sermelli':
            case 'sadad_meli':
                return $this->config->sadadBankId;
            case 'irankish':
                return $this->config->iranKishBankId;

            case 'mellatbank':
                return $this->config->mellatBankId;

            case 'parsianbank':
                return $this->config->parsianBankId;

            case 'zarinpal':
                return $this->config->zarinpalBankId;

            case 'zarinpal_sms':
                return $this->config->zarinpalSmsBankId;

            case 'zibal':
                return $this->config->zibalBankId;

            case 'asanpardakht':
                return $this->config->asanpardakhtBankId;

            case 'saman':
                $bankGateway = ($this->findBankGatewayByNameService)($transaction->payment_method);
                if (is_null($bankGateway) || is_null($bankGateway->rahkaran_id)) {
                    throw new BadRequestException(trans('rahkaran.error.NOT_FOUND_TRANSACTION_BANK_ACCOUNT_ID', [
                        'transaction_id' => $transaction->id
                    ]));
                }

                return $bankGateway->rahkaran_id;
            case 'client_credit':
                return $this->config->creditBankId;
            case 'offline_bank':
            case 'offline-bank':
            case 'offlinebank':
                $offlineTransaction = $transaction->offlineTransaction;
                if (!$offlineTransaction || !$offlineTransaction->bankAccount || !$offlineTransaction->bankAccount->rahkaran_id) {
                    throw new BadRequestException(trans('rahkaran.error.NOT_FOUND_TRANSACTION_BANK_ACCOUNT_ID', [
                        'transaction_id' => $transaction->id
                    ]));
                }
                return $offlineTransaction->bankAccount->rahkaran_id;
            default:
                return $this->config->defaultBankId;
        }
    }

    /**
     * Maps client details into given client party instance
     *
     * @param Client $client
     * @param RahkaranParty $client_party
     * @param array $ignored_fields
     * @return RahkaranParty
     */
    private function mapClientParty(Client $client, RahkaranParty $client_party, array $ignored_fields = []): RahkaranParty
    {
        $client_party->FirstName = $client->first_name;
        $client_party->LastName = $client->last_name;

        $national_code = !in_array('national_code', $ignored_fields);

        // Set national code if given client has a valid national code
        if ($national_code && !$client->is_foreigner && !empty($client->national_code) && strlen($client->national_code) == 10) {
            $client_party->NationalID = $client->national_code;
        }

        if ($client->is_legal) {

            if (!in_array('company_registered_number', $ignored_fields) && $client->company_registered_number) {

                $client_party->EconomicCode = $client->company_registered_number;

            } elseif (!in_array('company_national_code', $ignored_fields) && $client->company_national_code) {

                $client_party->EconomicCode = $client->company_national_code;
            }

        }

        $client_party->Type = $client->is_legal ? 1 : 0;
        $client_party->CompanyName = $client->company_name ?? $client->first_name . ' ' . $client->last_name;

        $address = !in_array('address', $ignored_fields);

        if ($client->is_legal || $address) {

            $company_addresses = [];

            $client_party_address = new PartyAddress();
            $client_party_address->Email = $client->email;
            $client_party_address->Phone = $client->phone_number ?? $client->mobile_number;
            $client_party_address->Name = trans('rahkaran.party.MAIN_ADDRESS');
            $client_party_address->RegionalDivisionRef = $this->getRegion($client->city);
            $client_party_address->Details = $client->is_legal == 0 ? $client->address : $client->company_address;
            $company_addresses[] = $client_party_address;

            $company_address = !in_array('company_address', $ignored_fields);

            if ($client->is_legal && $company_address && $client->company_phone_number && $client->company_city && $client->company_address) {
                $client_party_address = new PartyAddress();
                $client_party_address->Email = $client->email;
                $client_party_address->Phone = $client->company_phone_number ?? $client->mobile_number;
                $client_party_address->Name = trans('rahkaran.party.COMPANY_ADDRESS');
                $client_party_address->RegionalDivisionRef = $this->getRegion($client->company_city);
                $client_party_address->Details = $client->company_address;
                $company_addresses[] = $client_party_address;
            }

            $client_party->PartyAddresses = $company_addresses;
        }

        return $client_party;
    }

    /**
     * Creates party by given party object
     *
     * @param RahkaranParty $party
     * @return RahkaranParty|null
     */
    private function createParty(RahkaranParty $party): ?RahkaranParty
    {
        if ($this->isTestMode()) {
            $party->ID = 1;
            return $party;
        }

        $result = $this->makeRequest($this->baseUrl . '/General/PartyManagement/Services/PartyService.svc/GenerateParty', 'post', [
            $party->getAttributes()
        ], $this->getHeaders());

        $result = $this->validatePartyResult($result);

        $party->ID = $result['ID'];
        $party->Title = $result['Title'];

        return $party;
    }

    /**
     * Updates party by given party object
     *
     * @param RahkaranParty $party
     * @return RahkaranParty|null
     */
    private function updateParty(RahkaranParty $party): ?RahkaranParty
    {
        if ($this->isTestMode()) {
            return $party;
        }

        $result = $this->makeRequest($this->baseUrl . '/General/PartyManagement/Services/PartyService.svc/EditParty', 'post', [
            $party
        ], $this->getHeaders());

        $result = $this->validatePartyResult($result);

        return $this->getPartyById($result['ID']);
    }

    /**
     * Validates get party endpoint
     *
     * @param array $result
     * @return mixed
     */
    private function validatePartyResult(array $result)
    {
        if (count($result) < 1) {
            throw new FatalErrorException(trans('rahkaran.error.RESPONSE_ERROR'));
        }

        $result = $result[0];

        if (!isset($result['ID'])) {
            throw new FatalErrorException(trans('rahkaran.error.RESPONSE_PARSING_ERROR'));
        }

        if (isset($result['ValidationErrors']) && count($result['ValidationErrors']) > 0) {
            throw new BadRequestException(implode(',', $result['ValidationErrors']));
        }

        return $result;
    }

    /**
     * Gets party by given party id
     *
     * @param string $party_id
     * @return RahkaranParty|null
     */
    private function getPartyById(string $party_id): ?RahkaranParty
    {
        if ($this->isTestMode()) {
            return new RahkaranParty([
                'ID' => 1
            ]);
        }

        $result = $this->makeRequest($this->baseUrl . '/General/PartyManagement/Services/PartyService.svc/PartyByRef', 'post', [
            'partRef' => $party_id
        ], $this->getHeaders());

        if (!isset($result['GetPartyResult'])) {
            throw new FatalErrorException(trans('rahkaran.error.RESPONSE_ERROR'));
        }

        $result = $result['GetPartyResult'];

        if (!isset($result['ID'])) {
            throw new FatalErrorException(trans('rahkaran.error.RESPONSE_PARSING_ERROR'));
        }

        if ($result['ID'] == 0) {
            throw new BadRequestException(trans('rahkaran.error.NOT_FOUND_PARTY', [
                'party_id' => $party_id
            ]));
        }

        return new RahkaranParty($result);
    }

    /**
     * Generates dl for given client
     *
     * @param Client $client
     * @return DlObject|mixed|null
     */
    private function generateClientDl(Client $client): ?DlObject
    {
        $client_party = $this->getClientParty($client);
        $party_dl_code = $this->getClientDlPartyCode($client);
        $party_dl_type = $client->is_legal ? $this->config->legalPartyDlType : $this->config->personalPartyDlType;
        return $this->generatePartyDL($party_dl_code, $party_dl_type, $client_party, $client);
    }

    /**
     * Generates dl for given party
     *
     * @param string $party_dl_code
     * @param string $party_dl_type
     * @param RahkaranParty s$party
     * @return mixed
     */
    private function generatePartyDL(string $party_dl_code, string $party_dl_type, RahkaranParty $party, Client $client)
    {
        if ($this->isTestMode()) {
            return $this->getDl($party_dl_code);
        }

        $party_title = $client->id . '_' . ($client->is_legal ? $client->company_name : $client->first_name . ' ' . $client->last_name);

        $result = $this->makeRequest($this->baseUrl . '/Financial/COAManagement/Services/COAService.svc/GeneratePartyDL', 'post', [
            [
                'Code'        => $party_dl_code,
                'DLTypeRef'   => $party_dl_type,
                'Description' => $party_title,
                'Title'       => $party_title,
                'ReferenceID' => $party->ID,
                'Title_En'    => $party_title
            ]
        ], $this->getHeaders());

        if (count($result) < 1) {
            throw new FatalErrorException(trans('rahkaran.error.RESPONSE_ERROR'));
        }

        $result = $result[0];

        if (!isset($result['DLCode'])) {
            throw new FatalErrorException(trans('rahkaran.error.RESPONSE_PARSING_ERROR'));
        }

        if (isset($result['ValidationErrors']) && count($result['ValidationErrors']) > 0) {
            throw new BadRequestException(implode(',', $result['ValidationErrors']));
        }

        return $this->getDl($result['DLCode']);
    }

    /**
     * Gets client's dl code
     *
     * @param Client $client
     * @return string
     */
    private function getClientDlPartyCode(Client $client): string
    {
        return ($client->is_legal ? $this->config->legalPartyDlCode : $this->config->personalPartyDlCode) + $client->id;
    }

    /**
     * Finds and Gets single dl
     *
     * @param string $code
     * @return DlObject|null
     */
    public function getDl(string $code): ?DlObject
    {
        $result = $this->getDlList([
            $code
        ]);

        return count($result) ? $result[0] : null;
    }

    /**
     * Finds and Gets dl list
     *
     * @param array $codes
     * @return array|DlObject[]
     */
    private function getDlList(array $codes = []): array
    {
        if ($this->isTestMode()) {
            return collect($codes)->map(function ($dl_object) {
                return new DlObject([
                    'Code'  => $dl_object,
                    'Title' => $dl_object,
                ]);
            })->all();
        }

        $party = $this->makeRequest($this->baseUrl . '/Financial/COAManagement/Services/COAService.svc/GetDLsByCode', 'post', $codes, $this->getHeaders());

        return collect($party)->map(function ($dl_object) {
            return new DlObject($dl_object);
        })->all();
    }

    /**
     * Creates voucher by voucher object
     *
     * @param Voucher $voucher
     * @return array
     */
    private function createVoucher(Voucher $voucher): array
    {
        if ($this->isTestMode()) {
            return [
                'ID'   => 1,
                'Code' => 1
            ];
        }

        $result = $this->makeRequest($this->baseUrl . '/Financial/VoucherManagement/Services/VoucherService.svc/RegisterVoucher', 'post', $voucher, $this->getHeaders());

        Log::info('AddInvoiceToRahkaran ', $result ?? []);

        if (isset($result['ValidationErrors']) && $result['ValidationErrors'] && count($result['ValidationErrors']) > 0) {
            Log::error('Create Rahkaran Voucher ' . $voucher->Number, $result);
            throw new BadRequestException(implode(',', Arr::flatten($result['ValidationErrors'])));
        }

        if (!isset($result['ID']) && !$result['ID']) {
            throw new FatalErrorException(trans('rahkaran.error.RESPONSE_PARSING_ERROR'));
        }

        return $result;
    }

    /**
     * Validates receipt or payment result by given voucher object
     *
     * @param array $result
     * @return array
     * @todo
     */
    private function validateReceiptAndPayment(array $result): array
    {
        if (isset($result['ValidationErrors']) && $result['ValidationErrors'] && count($result['ValidationErrors']) > 0) {
            throw new BadRequestException(implode(',', $result['ValidationErrors']));
        }

        if (!isset($result['ID']) && !$result['ID']) {
            throw new FatalErrorException(trans('rahkaran.error.RESPONSE_PARSING_ERROR'));
        }

        return $result;
    }

    /**
     * @param Product $product
     * @return string
     */
    private function getProductLevel4DlCode(Product $product): string
    {
        return $this->config->productGroupDlCode + $product->product_group_id;
    }

    /**
     * @param Product $product
     * @return string
     */
    private function getProductLevel5DlCode(Product $product): string
    {
        return $this->config->productDlCode + $product->id;
    }

    /**
     * @return string
     */
    private function getDomainLevel4DlCode(): string
    {
        return $this->config->domainDl4Code;
    }

    /**
     * @param $tld
     * @return string
     */
    private function getDomainLevel5DlCode($tld): string
    {
        return in_array($tld, ['ir', 'co.ir']) ? $this->config->domainIrDl5Code : $this->config->domainIntDl5Code;
    }

    /**
     * @return DlObject
     */
    private function getAdminTimeLevel4Dl(): DlObject
    {
        $dl_code = $this->config->adminTimeDl4Code;

        $dl_object = $this->getDl(
            $this->config->adminTimeDl4Code
        );

        $description = trans('rahkaran.dl.ADMIN_TIME');

        if (!$dl_object) {
            $this->createDl((string)$dl_code, $this->config->level4DlType, $description, $description);
            return $this->getAdminTimeLevel4Dl();
        } else {
            return $dl_object;
        }
    }

    /**
     * @return DlObject
     */
    private function getAdminTimeLevel5Dl(): DlObject
    {
        $dl_code = $this->config->adminTimeDl5Code;

        $dl_object = $this->getDl(
            $dl_code
        );

        $description = trans('rahkaran.dl.ADMIN_TIME');

        if (!$dl_object) {
            $this->createDl((string)$dl_code, $this->config->level5DlType, $description, $description);
            return $this->getAdminTimeLevel5Dl();
        } else {
            return $dl_object;
        }
    }

    /**
     * @return DlObject
     */
    private function getCloudLevel4Dl(): DlObject
    {
        $dl_code = $this->config->cloudDl4Code;

        $dl_object = $this->getDl(
            $this->config->cloudDl4Code
        );

        $description = trans('rahkaran.dl.CLOUD');

        if (!$dl_object) {
            $this->createDl((string)$dl_code, $this->config->level4DlType, $description, $description);
            return $this->getCloudLevel4Dl();
        } else {
            return $dl_object;
        }
    }

    /**
     * @return DlObject
     */
    private function getCloudLevel5Dl(): DlObject
    {
        $dl_code = $this->config->cloudDl5Code;

        $dl_object = $this->getDl(
            $dl_code
        );

        $description = trans('rahkaran.dl.CLOUD');

        if (!$dl_object) {
            $this->createDl((string)$dl_code, $this->config->level5DlType, $description, $description);
            return $this->getCloudLevel5Dl();
        } else {
            return $dl_object;
        }
    }

    /**
     * Creates rahkaran dl
     *
     * @param string $code
     * @param $dl_type_ref
     * @param string $title
     * @param string $description
     * @return mixed
     */
    public function createDl(string $code, $dl_type_ref, string $title = '', string $description = ''): ?DlObject
    {
        if ($this->isTestMode()) {
            return $this->getDl($code);
        }

        $result = $this->makeRequest($this->baseUrl . '/Financial/COAManagement/Services/COAService.svc/RegisterDL',
            'post',
            [[
                'Code'        => $code,
                'DLTypeRef'   => $dl_type_ref,
                'Description' => $description,
                'Title'       => $title,
                'Title_En'    => ''
            ]],
            $this->getHeaders());

        $result = $this->validatePartyResult($result);

        if ($result['ID'] == 0) {
            throw new FatalErrorException(trans('rahkaran.error.NOT_FOUND_DL', [
                'code' => $code
            ]));
        }

        return $this->getDl($code);
    }

    /**
     * Gets Rahkaran region ID by given city
     *
     * @param string|null $city
     * @return int
     */
    private function getRegion(?string $city = ''): ?int
    {
        if (count($this->regions) == 0) {
            foreach ($this->getRegionalDivisionList() as $region_item) {
                switch ($region_item['Type']) {
                    case 1:
                        $this->regions['country'][$region_item['Name']] = $region_item;
                        break;
                    case 2:
                        $this->regions['state'][$region_item['Name']] = $region_item;
                        break;
                    case 3:
                        $this->regions['city'][$region_item['Name']] = $region_item;
                        break;
                }
            }
        }
        if (isset($this->regions['city'][$city])) {
            return $this->regions['city'][$city]['RegionalDivisionID'];

        } else {
            return $this->config->defaultRegionalDivisionID;
        }
    }

    /**
     * Gets regional division list from Rahkaran
     *
     * @return array[]
     */
    private function getRegionalDivisionList(): ?array
    {
        if ($this->isTestMode()) {
            return [
                [
                    'Name'               => 'Test City',
                    'Type'               => 3,
                    'RegionalDivisionID' => $this->config->defaultRegionalDivisionID
                ]
            ];
        }
        return $this->makeRequest($this->baseUrl . '/General/AddressManagement/Services/AddressManagementWebService.svc/GetRegionalDivisionList', 'get', [], $this->getHeaders());
    }

    // ########## ########## ##########
    // ####### Voucher Items Methods #######
    // ########## ########## ##########
    private function getAllTypesVoucherItem(Item $item, bool $is_refund): VoucherItem
    {
        if ($item->amount < 0) {
            throw new BadRequestException("Rahkaran negative service item {$item->invoice_id}");
        }

        $voucher_item = new VoucherItem();

        $level_4 = null;
        $level_5 = null;
        $level_6 = null;

        switch ($item->invoiceable_type) {
            case Item::TYPE_HOSTING:
            case Item::TYPE_PRODUCT_SERVICE:
            case Item::TYPE_PRODUCT_SERVICE_UPGRADE:
                $service = MainAppAPIService::getProductOrDomain('product', $item->invoiceable_id);
                $level_6 = $this->getTotalDL6Code('product', $service['product']);
                $level_5 = $this->getTotalDL5Code('product', $service['product']);
                $level_4 = $this->getTotalDL4Code('product', $service['product']['product_group']);
                break;
            case Item::TYPE_DOMAIN_SERVICE:
                // Todo: Load domain tld to find region
                $domain = MainAppAPIService::getProductOrDomain('domain', $item->invoiceable_id);
                $level_6 = $this->getTotalDL6Code('domain', null, $domain);
                $level_5 = $this->getTotalDL5Code('domain', null, $domain);
                $level_4 = $this->getTotalDL4Code('domain');
                break;
            case Item::TYPE_ADMIN_TIME:
                $level_6 = $this->getTotalDL6Code('adminTime');
                $level_5 = $this->getTotalDL5Code('adminTime');
                $level_4 = $this->getTotalDL4Code('adminTime');
                break;
            case Item::TYPE_CLOUD :
                $level_6 = $this->getTotalDL6Code('cloud');
                $level_5 = $this->getTotalDL5Code('cloud');
                $level_4 = $this->getTotalDL4Code('cloud');
                break;
            default:
                $level_6 = $this->getTotalDL6Code('global');
                $level_5 = $this->getTotalDL5Code('global');
                $level_4 = $this->getTotalDL4Code('global');
                break;
        }

        if ($level_4 && $level_5 && $level_6) {
            $voucher_item->DL4 = $level_4->Code;
            $voucher_item->DL5 = $level_5->Code;
            $voucher_item->DL6 = $level_6->Code;
            $voucher_item->DLLevel4Title = $level_4->Title;
            $voucher_item->DLLevel5Title = $level_5->Title;
            $voucher_item->DLLevel6Title = $level_6->Title;
        } else {
            $voucher_item->DL4 = $is_refund ? $this->config->refundDl4Code : $this->config->generalDl4Code;
            $voucher_item->DL5 = $this->config->generalDl5Code;
            $voucher_item->DL6 = $this->config->generalDl6Code;
        }

        $voucher_item->SLCode = $is_refund ? $this->config->refundSl : $this->config->saleSl;
        $voucher_item->{$is_refund ? 'Debit' : 'Credit'} = round($item->amount, 0, PHP_ROUND_HALF_DOWN);

        return $voucher_item;
    }

    /**
     * @param int $amount
     * @return null|VoucherItem
     */
    private function getRoundingVoucherItem(int $amount): ?VoucherItem
    {
        $voucher_item = new VoucherItem();

        $voucher_item->Description = trans('rahkaran.voucher_item.rounding');

        $voucher_item->SLCode = $this->config->roundingSl;

        $voucher_item->DL4 = $this->config->roundingDl4Code;

        $voucher_item->DL5 = $this->config->roundingDl5Code;

        if ($amount < 0) {

            $voucher_item->Credit = abs($amount);

        } else {

            $voucher_item->Debit = $amount;
        }

        return $voucher_item;
    }

    /**
     * Returns raw invoice toll-excluded tax (ex. 6%)
     *
     * @param Invoice $invoice
     * @return int|float
     */
    public function getRawInvoiceTax(Invoice $invoice)
    {

        $total_tax = round(abs($invoice->tax), 0, PHP_ROUND_HALF_DOWN);

        $tax_percent = config('tax.tax');

        $total_tax_percent = config('tax.total');

        return round(($total_tax * $tax_percent) / $total_tax_percent, 0, PHP_ROUND_HALF_DOWN);
    }

    /**
     * Returns raw invoice toll
     *
     * @param $invoice
     * @return int|float
     */
    public function getRawInvoiceToll($invoice)
    {
        $total_tax = $this->getRawInvoiceTotalTax($invoice);

        $toll_percent = config('tax.toll');

        $total_tax_percent = config('tax.total');

        return round(($total_tax * $toll_percent) / $total_tax_percent, 0, PHP_ROUND_HALF_DOWN);
    }

    /**
     * Returns invoice total tax
     *
     * @param $invoice
     * @return int|float
     */
    public function getRawInvoiceTotalTax(Invoice $invoice)
    {
        // @todo calculate by invoice items
        return round($invoice->tax > 0 ? $invoice->tax : 0);
    }

    /**
     * @param Item $item
     * @return int|null
     */
    private function getItemTax(Item $item): ?int
    {
        if ($item->amount == 0)
            return 0;

        $tax_percent = config('tax.tax');

        $tax = round(($tax_percent / 100) * $item->amount, 0, PHP_ROUND_HALF_DOWN);

        if ($tax <= 0) {
            var_dump('No Tax found for invoice: ' . $item->invoice_id);
        }

        return $tax;
    }

    /**
     * @param Item $item
     * @return int|null
     */
    private function getItemToll(Item $item): ?int
    {
        if ($item->amount == 0)
            return 0;

        $toll_percent = config('tax.toll');
        $toll = round(($toll_percent / 100) * $item->amount, 0, PHP_ROUND_HALF_DOWN);

        if ($toll <= 0) {
            var_dump('No Troll found for invoice: ' . $item->invoice_id);
        }

        return $toll;
    }

    /**
     * @param Invoice $invoice
     * @param null $client_rahkaran_id
     * @return VoucherItem|null
     */
    private function getNewTaxVoucherItem(Invoice $invoice, $client_rahkaran_id = null): ?VoucherItem
    {
        $totalTax = $this->getRawInvoiceTotalTax($invoice);
        $tax = $this->getRawInvoiceTax($invoice);
        $troll = $this->getRawInvoiceToll($invoice);

        if ($totalTax <= 0) {
            var_dump('No Troll found for invoice: ' . $invoice->id);
        }

        $voucher_item = new VoucherItem();
        $voucher_item->SLCode = $this->config->newTaxSl;
        $voucher_item->Description = trans('rahkaran.tax.newTax', [
            'tax' => $invoice->tax_rate
        ]);

        if ($client_rahkaran_id) {
            $voucher_item->PartyRef = $client_rahkaran_id;
            $voucher_item->TaxAmount = $tax; // TODO check this
            $voucher_item->TollAmount = $troll;
            $voucher_item->TaxStateType = 1;
            $voucher_item->PurchaseOrSale = 1;
            $voucher_item->ItemOrService = 2;
            $voucher_item->TransactionType = 1;
        }

        if ($invoice->status == Invoice::STATUS_REFUNDED) {
            $voucher_item->Debit = $totalTax;
        } else {
            $voucher_item->Credit = $totalTax;
        }

        return $voucher_item;
    }

    /**
     * @param int $client_dl_code
     * @param Invoice $invoice
     * @return null|VoucherItem
     */
    private function getRefundVoucherItem(int $client_dl_code, Invoice $invoice): ?VoucherItem
    {
        $amount = round($this->transactionRepository->sumOfPaidTransactions($invoice), 0, PHP_ROUND_HALF_DOWN);

        if ($amount <= 0) {
            return null;
        }

        $voucher_item = new VoucherItem();
        $voucher_item->SLCode = $this->config->paymentSl;
        $voucher_item->DL4 = $client_dl_code;
        $voucher_item->Credit = $amount;
        $voucher_item->Description = trans('rahkaran.voucher_item.refund');

        return $voucher_item;
    }

    /**
     * Returns and calculates raw invoice balance
     *
     * @param Invoice $invoice
     * @param bool $rounding_included
     * @return int|float
     */
    public function getRawInvoiceBalance(Invoice $invoice, bool $rounding_included): float|int
    {
        $total = $invoice->total;

        $payment_sum = $this->getRawInvoicePaymentsAmount($invoice, $rounding_included) ?? 0;

        return math_subtract($total, $payment_sum, $this->getDecimalPlaces());
    }

    /**
     * Returns number of decimal places
     *
     * @return int
     */
    public function getDecimalPlaces(): int
    {
        return 2;
    }


    /**
     * Calculates and returns sum of paid transactions
     *
     * @param Invoice $invoice
     * @param bool $rounding_included
     * @return float|int|null
     */
    public function getRawInvoicePaymentsAmount(Invoice $invoice, bool $rounding_included = true): float|int|null
    {
        $transaction_scope = [
            'status' => [
                $this->getActiveTransactionStatus()
            ]
        ];

        if (!$rounding_included) {
            $transaction_scope['withoutRounding'] = null;
        }

        return $this->transactionRepository->sum(
            'amount',
            [
                'invoice_id' => $invoice->id
            ],
            $transaction_scope
        );
    }

    /**
     * Returns list of active transaction status
     *
     * @return array
     */
    public function getActiveTransactionStatus(): array
    {
        return [
            Transaction::STATUS_SUCCESS,
            Transaction::STATUS_IPG_PAID,
            Transaction::STATUS_OPG_PAID,
        ];
    }

    /**
     * @param int $client_dl_code
     * @param Invoice $invoice
     * @return null|VoucherItem
     */
    private function getCollectionVoucherItem(int $client_dl_code, Invoice $invoice): ?VoucherItem
    {
        $amount = round($this->getRawInvoiceBalance($invoice, true));

        if ($amount <= 0) {
            return null;
        }

        $voucher_item = new VoucherItem();
        $voucher_item->SLCode = $this->config->collectionSl;
        $voucher_item->DL4 = $client_dl_code;
        $voucher_item->Debit = $amount;
        $voucher_item->Description = trans('rahkaran.voucher_item.collection');

        return $voucher_item;
    }

    /**
     * @param int $client_dl_code
     * @param Transaction $transaction
     * @return VoucherItem
     */
    private function getCreditTransactionVoucherItem(int $client_dl_code, Transaction $transaction): VoucherItem
    {
        $voucher_item = new VoucherItem();
        $voucher_item->SLCode = $this->config->creditSl;
        $voucher_item->DL4 = $client_dl_code;
        $voucher_item->Debit = round($transaction->amount);
        $voucher_item->Description = trans('rahkaran.voucher_item.credit', [
            'transaction_id' => $transaction->id
        ]);

        return $voucher_item;
    }

    /**
     * @param $client_dl_code
     * @param Transaction $transaction
     * @return VoucherItem
     */
    private function getPaymentTransactionVoucherItem($client_dl_code, Transaction $transaction): VoucherItem
    {
        $voucher_item = new VoucherItem();
        $voucher_item->SLCode = $this->config->paymentSl;
        $voucher_item->DL4 = $client_dl_code;
        $voucher_item->Debit = round($transaction->amount);
        $voucher_item->Description = trans('rahkaran.voucher_item.payment', [
            'transaction_id' => $transaction->id
        ]);
        return $voucher_item;
    }

    /**
     * @param $amount
     * @return VoucherItem
     */
    private function getPaymentFeeItem($amount): VoucherItem
    {
        $voucher_item = new VoucherItem();

        $voucher_item->SLCode = $this->config->paymentFeeSl;
        $voucher_item->DL4 = $this->config->paymentFeeDL4;
        $voucher_item->DL5 = $this->config->paymentFeeDL5;
        $voucher_item->Debit = round($amount, 0, PHP_ROUND_HALF_DOWN);
        $voucher_item->Description = 'پرداخت کارمزد از موجودی';
        $voucher_item->Description_En = 'پرداخت کارمزد از موجودی';

        return $voucher_item;
    }

    /**
     * @param $item
     * @param $amount
     * @return VoucherItem
     */
    private function getPaymentFeeTransactionVoucherItem($item, $amount): VoucherItem
    {
        $voucher_item = new VoucherItem();
        $voucher_item->SLCode = $this->config->bankBaseSl;
        $voucher_item->DL4 = $this->config->zarinpalDL4;
        $voucher_item->Credit = round($amount);

        $voucher_item->Description = 'کارمزد انتقال وجه به مشتری با کد پیگیری ' . $item->payout_id;
        $voucher_item->Description_En = 'کارمزد انتقال وجه به مشتری با کد پیگیری ' . $item->payout_id;
        return $voucher_item;
    }

    // ########## ########## ##########
    // ####### Unused Methods #######
    // ########## ########## ##########
    /**
     * Validates voucher by given voucher object
     *
     * @param Voucher $voucher
     * @return mixed|void
     * @todo
     */
    private function validateVoucher(Voucher $voucher)
    {
        if ($this->isTestMode()) {
            return [
                []
            ];
        }

        return $this->makeRequest($this->baseUrl . '/Financial/VoucherManagement/Services/VoucherService.svc/ValidateVoucher', 'post', $voucher, $this->getHeaders());
    }

    /**
     * @param $dl
     * @return mixed
     */
    private function checkPartyDLExistence($dl)
    {
        return $this->makeRequest($this->baseUrl . '/Financial/COAManagement/Services/COAService.svc/CheckDLExistance', 'post', [
            $dl
        ], $this->getHeaders());
    }

    /**
     * @return array|mixed
     */
    private function getAllDLTypes(): array
    {
        if ($this->isTestMode()) {
            return [
                [
                    'ID'   => 1,
                    'Code' => 1
                ]
            ];
        }

        return $this->makeRequest($this->baseUrl . '/Financial/COAManagement/Services/COAService.svc/GetAllDLTypes', 'get', [], $this->getHeaders());
    }

    /**
     * @return array
     */
    private function getAllLeadingLedgerActiveSLs(): array
    {
        if ($this->isTestMode()) {
            return [
                'ID'   => 1,
                'Code' => 1
            ];
        }

        return $this->makeRequest($this->baseUrl . '/Financial/COAManagement/Services/COAService.svc/GetAllLeadingLeadgerActiveSLs', 'get', [], $this->getHeaders());
    }

    /**
     * @return int[]
     */
    private function getCompanyDLTypeID(): array
    {
        if ($this->isTestMode()) {
            return [
                'ID'   => 1,
                'Code' => 1
            ];
        }

        return $this->makeRequest($this->baseUrl . '/Financial/COAManagement/Services/COAService.svc/GetCompanyDLTypeID', 'get', [], $this->getHeaders());
    }

    /**
     * @param $voucher_id
     * @return int[]|array|null
     */
    private function getVoucher($voucher_id): array
    {
        if ($this->isTestMode()) {
            return [
                'ID' => 1
            ];
        }

        return $this->makeRequest($this->baseUrl . '/Financial/VoucherManagement/Services/VoucherService.svc/ViewVoucher?id=' . $voucher_id, 'get', [], $this->getHeaders());
    }

    // ########## ########## ##########
    // ####### Create Request #######
    // ########## ########## ##########
    /**
     * @param $url
     * @param $method
     * @param $body
     * @param $headers
     * @return mixed
     */
    private function makeRequest($url, $method, $body, $headers)
    {
        $encoded_body = json_encode($body);

        $log_system = $this->createRequestLog($method, $url, $encoded_body, $headers);

        try {

            $params = [
                'headers' => $headers,
                'body'    => $encoded_body
            ];

            $params['cookies'] = CookieJar::fromArray([
                'sg-auth-hostiran' => $this->sessionId
            ], config('rahkaran.rahkaran_host'));

            $response = $this->client->request(
                $method,
                $url,
                $params
            );

        } catch (ClientException $exception) {
            $content = $exception->getResponse()->getBody()->getContents();
            $this->updateRequestLog($log_system, $content ?? $exception->getMessage(), $exception->getResponse()->getHeaders(), $exception->getResponse()->getStatusCode());
            Log::error("result $url " . $exception->getMessage(), [$content ?? '']);
            throw new FatalErrorException($exception->getMessage());
        } catch (ServerException $exception) {
            $content = $exception->getResponse()->getBody()->getContents();
            Log::error("result $url " . $exception->getMessage(), [$exception->getResponse()->getBody()->getContents() ?? '']);
            $this->updateRequestLog($log_system, $content ?? $exception->getMessage(), $exception->getResponse()->getHeaders(), $exception->getResponse()->getStatusCode());
            throw new FatalErrorException($exception->getMessage());
        } catch (Throwable $exception) {
            Log::error("result $url " . $exception->getMessage(), $exception->getTrace());

            $this->updateRequestLog($log_system, [
                $exception->getMessage()
            ], [], 500);
            throw new FatalErrorException($exception->getMessage());
        }

        $response_body = $response->getBody()->getContents();

        $this->updateRequestLog($log_system, $response_body, $response->getHeaders(), $response->getStatusCode());

        Log::info("result $url", json_decode($response_body, true) ?? []);
        return json_decode($response_body, true);
    }


    /**
     * @return string[]
     */
    private function getHeaders(): array
    {
        return [
            'Accept'       => 'Application/json',
            'Content-Type' => 'Application/json'
        ];
    }

    /**
     * @param $method
     * @param $url
     * @param $requestBody
     * @param $headers
     * @return AbstractBaseLog
     */
    private function createRequestLog($method, $url, $requestBody, $headers): AbstractBaseLog
    {
        $requestBody = $requestBody && is_string($requestBody) && is_json($requestBody) ? json_decode($requestBody, true) : $requestBody;

        return LogService::store(SystemLog::make(), [
            'method'         => $method,
            'endpoint'       => SystemLog::ENDPOINT_RAHKARAN,
            'request_url'    => $url,
            'request_body'   => $requestBody,
            'request_header' => json_encode($headers),
            'provider'       => SystemLog::PROVIDER_OUTGOING,
        ]);

    }

    /**
     * @param $systemLog
     * @param $responseBody
     * @param array $getHeaders
     * @param int $getStatusCode
     * @return void
     */
    private function updateRequestLog($systemLog, $responseBody, array $getHeaders, int $getStatusCode): void
    {
        if ($systemLog instanceof AbstractBaseLog) {
            $responseBody = $responseBody && is_string($responseBody) && is_json($responseBody) ? json_decode($responseBody, true) : $responseBody;

            $custom_response = [
                'header' => $getHeaders,
                'body'   => $responseBody,
                'status' => $getStatusCode
            ];

            dispatch(new UpdateSystemLog($systemLog, $custom_response))->onQueue(Queue::SYSTEM_LOG_QUEUE);
        }
    }

    /**
     * @param CreditTransaction $credit_transaction
     * @return Receipt
     */
    private function getReceiptInstance(CreditTransaction $credit_transaction): Receipt
    {
        $config = $this->getConfig();

        // Fetches client party dl from rahkaran service and generate party and its dl if the party not exists
        $client = MainAppAPIService::getClients($credit_transaction->profile->client_id)[0];
        $client_party_dl = $this->getClientDl($client);

        $receipt = new Receipt();
        $receipt->BranchID = $config->bankBranchId;
        $receipt->IsApproved = true;
        $receipt->CounterPartDLCode = $client_party_dl->Code;
        $receipt->Date = $credit_transaction->created_at;

        // Creates new ReceiptDeposit instance
        $receipt_deposit = new ReceiptDeposit();

        $receipt_deposit->Amount = $credit_transaction->amount;

        $receipt_deposit->AccountingOperationID = $config->receiptAccountingOperationID;
        $receipt_deposit->CashFlowFactorID = $config->receiptCashFlowFactorID;
        $receipt_deposit->BankAccountID = $config->creditBankId;

        $receipt_deposit->CounterPartDLCode = $client_party_dl->Code;

        $receipt_deposit->Number = 'Credit-' . $credit_transaction->id;

        // Uses given description or gets from translation
        $description = $credit_transaction->description ? $credit_transaction->description : trans('rahkaran.receipt.ADD_CREDIT');

        $receipt->Description = $description;
        $receipt->Description_En = $description;

        $receipt_deposit->Description = $description;
        $receipt_deposit->Description_En = $description;
        $receipt_deposit->Date = $credit_transaction->created_at;

        $receipt->ReceiptDeposits = [
            $receipt_deposit
        ];

        return $receipt;
    }

    public function storeReceipt(CreditTransaction $credit_transaction): void
    {
        if (!$this->isTestMode()) {

            // Gets receipt instance to send to rahkaran
            $receipt = $this->getReceiptInstance(
                $credit_transaction
            );

            // Export transaction to Rahkaran system and throws an exception if it fails
            $this->createReceipt($receipt);
        }
    }

    public function storePayment(CreditTransaction $credit_transaction): void
    {
        if (!$this->isTestMode()) {

            // Gets payment instance to send to rahkaran
            $payment = $this->getPaymentInstance(
                $credit_transaction
            );

            // Export transaction to Rahkaran system and throws an exception if it fails
            $this->createPayment($payment);
        }
    }

    private function getPaymentInstance(CreditTransaction $credit_transaction)
    {
        $config = $this->getConfig();

        // Fetches client party dl from rahkaran service and generate party and its dl if the party not exists
        $client = MainAppAPIService::getClients($credit_transaction->profile->client_id)[0];
        $client_party_dl = $this->getClientDl($client);

        $payment = new Payment();
        $payment->BranchID = $config->bankBranchId;
        $payment->IsApproved = true;
        $payment->CounterPartDLCode = $client_party_dl->Code;
        $payment->Date = $credit_transaction->created_at;

        // Creates new PaymentDeposit instance
        $payment_deposit = new PaymentDeposit();

        $payment_deposit->Amount = abs($credit_transaction->amount);

        $payment_deposit->AccountingOperationID = $config->paymentAccountingOperationID;
        $payment_deposit->CashFlowFactorID = $config->paymentCashFlowFactorID;
        $payment_deposit->BankAccountID = $config->creditBankId;

        $payment_deposit->CounterPartDLCode = $client_party_dl->Code;

        $payment_deposit->Number = 'Credit-' . $credit_transaction->id;

        // Uses given description or gets from translation
        $description = $credit_transaction->description ? $credit_transaction->description : trans('rahkaran.payment.REMOVE_CREDIT');

        $payment->Description = $description;
        $payment->Description_En = $description;

        $payment_deposit->Description = $description;
        $payment_deposit->Description_En = $description;
        $payment_deposit->Date = $credit_transaction->created_at;

        $payment->PaymentDeposits = [
            $payment_deposit
        ];

        return $payment;
    }

    public function getPaymentInstanceForCashout(CreditTransaction $credit_transaction): Payment
    {
        $config = $this->getConfig();

        // Fetches client party dl from rahkaran service and generate party and its dl if the party not exists
        $client = MainAppAPIService::getClients($credit_transaction->profile->client_id)[0];
        $client_party_dl = $this->getClientDl($client);

        $payment = new Payment();
        $payment->BranchID = $config->bankBranchId;
        $payment->IsApproved = true;
        $payment->CounterPartDLCode = $client_party_dl->Code;
        $payment->Date = $credit_transaction->created_at;

        // Creates new PaymentDeposit instance
        $payment_deposit = new PaymentDeposit();
        $payment_deposit->Amount = abs($credit_transaction->amount);
        $payment_deposit->AccountingOperationID = $config->paymentAccountingOperationID;
        $payment_deposit->CashFlowFactorID = $config->paymentCashFlowFactorID;
        $payment_deposit->BankAccountID = $config->zarinpalBankId;
        $payment_deposit->CounterPartDLCode = $client_party_dl->Code;
        $payment_deposit->Number = 'Credit-' . $credit_transaction->id;

        // Uses given description or gets from translation
        $description = $credit_transaction->description ? $credit_transaction->description : trans('rahkaran.payment.REMOVE_CREDIT');

        $payment->Description = $description;
        $payment->Description_En = $description;

        $payment_deposit->Description = $description;
        $payment_deposit->Description_En = $description;
        $payment_deposit->Date = $credit_transaction->created_at;

        $payment->PaymentDeposits = [
            $payment_deposit
        ];

        return $payment;
    }

    private function isRoundingTransaction(Transaction $transaction): bool
    {
        return $transaction->reference_id == 'ROUND-' . $transaction->invoice->id;
    }

    private function isBarterTransaction(Transaction $transaction): bool
    {
        return $transaction->payment_method == Transaction::PAYMENT_METHOD_BARTER;
    }

    /**
     * @param $type
     * @param $productGroup
     * @return DlObject
     */
    private function getTotalDL4Code($type = 'domain', $productGroup = null)
    {
        $code = 90000000; // omomie sathe 4
        $description = 'تفضیلی عمومی سطح چهار';
        switch ($type) {
            case 'domain':
                $code = 90000201;
                $description = 'دامنه';
                break;
            case 'cloud':
                $code = 90000202;
                $description = 'ابر';
                break;
            case 'adminTime':
                $code = 90000205;
                $description = 'خدمات';
                break;
            case 'other':
                $code = 90000000;
                $description = 'عمومی سطح چهار';
                break;
            case 'product':
                if (!isset($productGroup) || !isset($productGroup['name'])) {
                    break;
                }
                if (Str::contains($productGroup['name'], ['Reseller', 'نمایندگی'])) {
                    $code = 90000206;
                    $description = 'سرویس نمایندگی';
                    break;
                }
                if (Str::contains($productGroup['name'], ['host', 'Host', 'هاست'])) {
                    $code = 90000203;
                    $description = 'خدمات میزبانی';
                    break;
                }
                if (Str::contains($productGroup['name'], ['Network', 'IXP', 'ixp'])) {
                    $code = 90000204;
                    $description = 'ارتباطات';
                    break;
                }
                // Other
                $code = 90000205;
                $description = 'خدمات';
                break;
        }

        $dl_object = $this->getDl($code);

        if (!$dl_object) {
            $this->createDl((string)$code, $this->config->level4DlType, $description, $description);
            return $this->getTotalDL4Code($type, $productGroup);
        } else {
            return $dl_object;
        }
    }

    /**
     * @param $type
     * @param $product
     * @param $domain
     * @return DlObject
     */
    private function getTotalDL5Code($type = 'domain', $product = null, $domain = null)
    {

        $code = 50001000; // omomie sathe 5
        $description = 'تفضیلی عمومی سطح پنج';

        switch ($type) {
            case 'domain':
                if (!isset($domain) || !isset($domain['registrar'])) {
                    break;
                }
                if (Str::contains($domain['registrar']['name'], ['irnic', 'Irnic'])) {
                    $code = 50002003;
                    $description = 'دامنه ir';
                } else {
                    $code = 50002002;
                    $description = 'دامنه بین المللی';
                }
                break;

            case 'cloud':
                $code = 50001063;
                $description = 'سرور مجازی';
                break;
            case 'adminTime':
                $code = 50002004;
                $description = 'هزینه خدمات کارشناس';
                break;
            case 'product':
                if (!isset($product) || !isset($product['name'])) {
                    break;
                }
                if (Str::contains($product['name'], ['خدمات مدیریت'])) {
                    $code = 50002004;
                    $description = 'هزینه خدمات کارشناس';
                    break;
                }
                if (Str::contains($product['name'], ['نمایندگی'])) {
                    $code = 50001124;
                    $description = 'نمایندگی هاست لینوکس';
                    break;
                }
                if ($product['product_group']['name'] == 'transactional-email') {
                    $code = 50002001;
                    $description = 'سرویس های ایمیل';
                    break;
                }
                if ($product['name'] == 'Backup Storage') {
                    $code = 50001156;
                    $description = 'Backup Storage';
                    break;
                }
                if (Str::contains($product['product_group']['name'], ['ssl-certificate', 'License']) || Str::contains($product['name'], ['ssl-certificate', 'License'])) {
                    $code = 50002014;
                    $description = 'لایسنس و گواهی ها';
                    break;
                }
                if (Str::contains($product['name'], ['Co-Location', 'Co Location'])) {
                    $code = 50002015;
                    $description = 'فضای اشتراکی';
                    break;
                }
                if (Str::contains($product['name'], ['سرویس مانیتورینگ'])) {
                    $code = 50002004;
                    $description = 'سرویس مانیتورینگ';
                    break;
                }
                if (Str::contains($product['name'], ['فروش ترافیک'])) {
                    $code = 50002005;
                    $description = 'ترافیک IXP';
                    break;
                }
                if (Str::contains($product['name'], ['Transmission', 'Radio'])) {
                    $code = 50002006;
                    $description = 'سرویس های انتقال';
                    break;
                }
                if (Str::contains($product['product_group']['name'], ['Windows-host'])) {
                    $code = 50002007;
                    $description = 'هاست ویندوز';
                    break;
                }
                if (Str::contains($product['product_group']['name'], ['Host-backup-ir'])) {
                    $code = 50001129;
                    $description = 'هاست بک آپ';
                    break;
                }
                if (Str::contains($product['product_group']['name'], ['Host-Download'])) {
                    $code = 50001108;
                    $description = 'هاست دانلود';
                    break;
                }
                if (Str::contains($product['product_group']['name'], ['Host-linux', 'Host-Linux'])) {
                    $code = 50002008;
                    $description = 'هاست لینوکس';
                    break;
                }
                if (Str::contains($product['product_group']['name'], ['wordpress', 'WordPress'])) {
                    $code = 50002009;
                    $description = 'هاست وردپرس';
                    break;
                }
                if (Str::contains($product['product_group']['name'], ['anycast', 'Anycast'])) {
                    $code = 50002010;
                    $description = 'هاست Anycast';
                    break;
                }
                if (Str::contains($product['product_group']['name'], ['dedicate', 'Dedicate'])) {
                    $code = 50002011;
                    $description = 'سرور اختصاصی';
                    break;
                }
                if (Str::contains($product['product_group']['name'], ['Host-Framework-IR'])) {
                    $code = 50002012;
                    $description = 'هاست فریم ورک';
                    break;
                }
                break;
        }


        $dl_object = $this->getDl(
            $code
        );
        if (!$dl_object) {
            $this->createDl((string)$code, $this->config->level5DlType, $description, $description);
            return $this->getTotalDL5Code($type, $product, $domain);
        } else {
            return $dl_object;
        }

    }

    /**
     * @param $type
     * @param $product
     * @param $domain
     * @return DlObject
     */
    private function getTotalDL6Code($type = 'domain', $product = null, $domain = null)
    {

        $code = 60001000;
        $description = 'تفصیلی عمومی سطح شش';

        switch ($type) {
            case 'domain':

                if (!isset($domain) || !isset($domain['registrar'])) {
                    break;
                }

                $code = $this->config->generalDl6Code + $domain['registrar']['id'];
                $description = 'شرکت ' . ($domain['registrar']['name'] != "none" ? $domain['registrar']['name'] : 'متفرقه');
                break;

            case 'cloud':
                $code = 60002000;
                $description = 'سرورهای ابری';

                // @todo: Base on type of cloud must change later
                break;

            case 'adminTime':
                $code = 60004000;
                $description = 'خدمات کارشناس هاست ایران';
                break;

            case 'product':
                if (!isset($product) || !isset($product['name'])) {
                    break;
                }
                $code = 60003000 + $product['id'];
                $description = 'محصول ' . $product['name'];
                break;
        }

        $dl_object = $this->getDl(
            $code
        );

        if (!$dl_object) {
            $this->createDl((string)$code, $this->config->level6DlType, $description, $description);
            return $this->getTotalDL6Code($type, $product, $domain);
        } else {
            return $dl_object;
        }
    }
}
