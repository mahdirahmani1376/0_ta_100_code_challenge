<?php

namespace App\Http\Requests\Invoice\Transaction;

use App\Models\BankGateway;
use App\Models\Transaction;
use App\Services\BankGateway\IndexBankGatewayService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var IndexBankGatewayService $indexBankGatewayService */
        $indexBankGatewayService = app(IndexBankGatewayService::class);
        $allowedPaymentMethods = $indexBankGatewayService([
            'export' => 1,
        ])->pluck('name')->toArray();

        $allowedPaymentMethods = array_merge($allowedPaymentMethods, Transaction::PAYMENT_METHODS);

        return [
            'invoice_id'     => ['required', Rule::exists('invoices', 'id'),],
            'amount'         => ['required', 'numeric', 'gte:0'],
            'reference_id'   => [
                'nullable',
                'string',
                Rule::unique('transactions', 'reference_id')->where('invoice_id', request('invoice_id'))
            ],
            'description'    => ['nullable', 'string'],
            'payment_method' => [
                'required',
                Rule::in($allowedPaymentMethods)
            ],
            'tracking_code'  => ['required', 'string'],
            'created_at'     => ['filled', 'date',],
        ];
    }
}
