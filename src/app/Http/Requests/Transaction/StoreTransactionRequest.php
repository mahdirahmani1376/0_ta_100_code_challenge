<?php

namespace App\Http\Requests\Transaction;

use App\Models\BankGateway;
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

        return [
            'invoice_id' => ['required', Rule::exists('invoices', 'id'),],
            'amount' => ['required', 'numeric', 'gte:0'],
            'reference_id' => [
                'nullable',
                'string',
                Rule::unique('transactions', 'reference_id')->where('invoice_id', request('invoice_id'))
            ],
            'description' => ['nullable', 'string'],
            'payment_method' => [
                'required',
                Rule::in(($indexBankGatewayService(['status' => BankGateway::STATUS_ACTIVE,]))->pluck('name')->toArray())
            ],
            'tracking_code' => ['required', 'string'],
            'created_at' => [
                'filled',
                'date_format:Y-m-d H:i:s',
            ],
        ];
    }
}
