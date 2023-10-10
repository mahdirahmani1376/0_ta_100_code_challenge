<?php

namespace App\Http\Requests\Profile\Invoice;

use App\Models\OfflineTransaction;
use App\Rules\ValidIRMobile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOfflineTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'integer',],
            'tracking_code' => ['required', 'unique:offline_transactions',],
            'payment_method' => ['required', 'string', Rule::in(OfflineTransaction::PAYMENT_METHODS),],
            'description' => ['nullable', 'string'],
            'bank_account_id' => ['required', 'exists:bank_accounts,id',],
            'mobile' => ['nullable', new ValidIRMobile,],
            'paid_at' => ['required', 'bail', 'date_format:Y-m-d', 'before:tomorrow',],
            'client_id' => ['required', 'numeric',],
        ];
    }
}
