<?php

namespace App\Http\Requests\Admin\Transaction;

use App\Exceptions\Http\BadRequestException;
use App\Models\BankGateway;
use App\Models\Invoice;
use App\Services\BankGateway\IndexBankGatewayService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Invoice $invoice */
        $invoice = request('invoice');
        if ($invoice->balance < 0 ) {
            throw new BadRequestException(
                trans('finance.invoice.NegativeBalance')
            );
        }
        if ($invoice->balance == 0 || request('amount') > $invoice->balance) {
            throw new BadRequestException(
                trans('finance.invoice.AmountExceedsInvoiceBalance')
            );
        }

        return true;
    }

    /**
     * @throws BindingResolutionException
     */
    public function rules(): array
    {
        /** @var IndexBankGatewayService $indexBankGatewayService */
        $indexBankGatewayService = app(IndexBankGatewayService::class);

        return [
            'amount' => ['required', 'numeric', 'gte:0'],
            'reference_id' => [
                'nullable',
                'string',
                Rule::unique('transactions', 'reference_id')->where('invoice_id', request('invoice')->getKey())
            ],
            'description' => ['nullable', 'string'],
            'payment_method' => [
                'required',
                Rule::in(($indexBankGatewayService(true))->pluck('name')->toArray())
            ],
            'tracking_code' => ['required', 'string'],
            'created_at' => [
                'filled',
                'date_format:Y-m-d H:i:s',
            ],
        ];
    }
}
