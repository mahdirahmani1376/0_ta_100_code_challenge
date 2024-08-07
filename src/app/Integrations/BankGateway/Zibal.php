<?php
// TODO maybe use Dedicated Exception class for each payment provider ?
// TODO refactor duplicate logics into an AbstractBase class
// TODO e.g. on callback when the payment has been unsuccessful we always set the status of transaction to fail and return,
// TODO this should not be repeated in each implementation of IPG providers
namespace App\Integrations\BankGateway;

use App\Exceptions\Http\BadRequestException;
use App\Models\BankGateway;
use App\Models\Transaction;
use App\Services\Transaction\UpdateTransactionService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

// Zibal api response:
//--------------------------------------- "result" value map ---------------------------------------
//100 	با موفقیت تایید شد.
//102 	merchant یافت نشد.
//103 	merchant غیرفعال
//104 	merchant نامعتبر
//105 	amount بایستی بزرگتر از 1,000 ریال باشد.
//106 	callbackUrl نامعتبر می‌باشد. (شروع با http و یا https)
//113 	amount مبلغ تراکنش از سقف میزان تراکنش بیشتر است.
//202 	سفارش پرداخت نشده یا ناموفق بوده است. جهت اطلاعات بیشتر جدول وضعیت‌ها را مطالعه کنید.
//201 	قبلا تایید شده.
//203 	trackId نامعتبر می‌باشد
//--------------------------------------- "status" value map ---------------------------------------
//-1 	در انتظار پردخت
//-2 	خطای داخلی
//1 	پرداخت شده - تاییدشده
//2 	پرداخت شده - تاییدنشده
//3 	لغوشده توسط کاربر
//4 	‌شماره کارت نامعتبر می‌باشد.
//5 	‌موجودی حساب کافی نمی‌باشد.
//6 	رمز واردشده اشتباه می‌باشد.
//7 	‌تعداد درخواست‌ها بیش از حد مجاز می‌باشد.
//8 	‌تعداد پرداخت اینترنتی روزانه بیش از حد مجاز می‌باشد.
//9 	مبلغ پرداخت اینترنتی روزانه بیش از حد مجاز می‌باشد.
//10 	‌صادرکننده‌ی کارت نامعتبر می‌باشد.
//11 	‌خطای سوییچ
//12 	کارت قابل دسترسی نمی‌باشد.
class Zibal extends BaseBankGateway implements Interface\BankGatewayInterface
{
    private UpdateTransactionService $updateTransactionService;

    public function __construct(
        private readonly BankGateway $bankGateway,
    )
    {
        $this->updateTransactionService = app(UpdateTransactionService::class);
    }

    public static function make(BankGateway $bankGateway): Interface\BankGatewayInterface
    {
        return new static($bankGateway);
    }

    public function getRedirectUrlToGateway(Transaction $transaction, string $callbackUrl): string
    {
        $response = Http::withHeader('Accept-Encoding', 'application/json')
            ->post($this->bankGateway->config['request_url'], [
                'merchant'    => $this->bankGateway->config['merchant_id'],
                'amount'      => $transaction->amount,
                'callbackUrl' => $callbackUrl,
            ]);

        if ($response->json('result') != 100) {
            ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
            // redirect to failed transaction page
//            throw new BadRequestException('Zibal failed at start, result: ' . $response->json('result'));
            return $this->getFailedRedirectUrl($transaction, $callbackUrl);
        }

        ($this->updateTransactionService)($transaction, ['tracking_code' => $response->json('trackId'),]);

        return Str::finish($this->bankGateway->config['start_url'], '/') . $response->json('trackId');
    }

    public function callbackFromGateway(Transaction $transaction, array $data): Transaction
    {
        $this->callbackLog($transaction, $data);
        if (!$data['success']) {
            return ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
        }
        if ($data['trackId'] != $transaction->tracking_code) {
            ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FRAUD]);
            throw new BadRequestException("Zibal miss match tracking_code, transactionId: $transaction->id , trackId: " . $data['trackId']);
        }

        $response = Http::withHeader('Accept-Encoding', 'application/json')
            ->post($this->bankGateway->config['verify_url'], [
                'merchant' => $this->bankGateway->config['merchant_id'],
                'trackId'  => $transaction->tracking_code,
            ]);

        if ($response->json('result') != 100) {
            ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
            throw new BadRequestException('Zibal result: ' . $response->json('result')); // TODO maybe use a custom exception class
        }

        if ($response->json('amount') != $transaction->amount) {
            ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FRAUD,]);
            throw new BadRequestException('Zibal status: ' . $response->json('status')); // TODO maybe use a custom exception class
        }

        return ($this->updateTransactionService)($transaction, [
            'status'       => Transaction::STATUS_SUCCESS,
            'reference_id' => $response->json('refNumber'),
        ]);
    }
}
