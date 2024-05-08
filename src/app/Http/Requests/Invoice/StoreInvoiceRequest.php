<?php

namespace App\Http\Requests\Invoice;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'admin_id'                 => ['nullable', 'integer',],
            'profile_id'               => ['required', 'integer', 'exists:profiles,id',],
            'invoice_date'             => ['nullable', 'date',],
            'due_date'                 => ['nullable', 'date',],
            'paid_at'                  => ['nullable', 'date',],
            'tax_rate'                 => ['nullable', 'integer',],
            'status'                   => ['required', Rule::in([
                Invoice::STATUS_UNPAID,
                Invoice::STATUS_DRAFT,
                Invoice::STATUS_REFUNDED,
            ])],
            'items'                    => ['required', 'array'],
            'items.*.description'      => ['required', 'string'],
            'items.*.amount'           => ['required', 'numeric'],
            'items.*.invoiceable_type' => ['nullable', 'string'],
            'items.*.invoiceable_id'   => ['nullable', 'integer'],
            'items.*.from_date'        => ['nullable', 'date',],
            'items.*.to_date'          => ['nullable', 'date',],
            'note'                     => ['nullable',],
        ];
    }
}
