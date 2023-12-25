<?php

namespace App\Http\Requests\Invoice\OfflineTransaction;

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
            'invoice_id' => ['required', 'exists:invoices,id',],
            'amount' => ['required', 'integer',],
            'payment_method' => ['required', 'string', Rule::in(OfflineTransaction::PAYMENT_METHODS),],
            'mobile' => ['nullable', new ValidIRMobile,],
            'paid_at' => ['required', 'date_format:Y-m-d', 'before:tomorrow',],
            'description' => ['nullable', 'string',],
            'tracking_code' => ['required', 'unique:offline_transactions',],
            'bank_account_id' => ['required', 'exists:bank_accounts,id',],
            'profile_id' => ['required', 'integer', 'exists:profiles,id',],
            'admin_id' => ['filled', 'integer',],
        ];
    }
}
