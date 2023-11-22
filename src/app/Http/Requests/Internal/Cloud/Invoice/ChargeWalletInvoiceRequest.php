<?php

namespace App\Http\Requests\Internal\Cloud\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class ChargeWalletInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'profile_id' => ['required', 'numeric', 'exists:profiles,id',],
            'item' => [
                'amount' => ['required', 'numeric',],
                'invoiceable_type' => ['required',],
                'invoiceable_id' => ['required',],
                'description' => ['nullable',],
            ],
        ];
    }
}
