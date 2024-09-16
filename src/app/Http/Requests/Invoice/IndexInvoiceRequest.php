<?php

namespace App\Http\Requests\Invoice;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sort'                     => ['nullable', 'max:255', Rule::in(get_sortable_items((new Invoice())->getFillable())),],
            'sort_direction'           => ['nullable', 'max:255', Rule::in('desc', 'asc'),],
            'per_page'                 => ['nullable', 'integer',],
            'profile_id'               => ['nullable', 'integer',],
            'invoice_id'               => ['nullable', 'integer',],
            'invoice_ids'              => ['nullable', 'array',],
            'invoice_ids.*'            => ['integer',],
            'payment_method'           => ['nullable', 'max:255',],
            'status'                   => ['nullable', 'max:255', Rule::in([...Invoice::STATUSES, 'all']),],
            'in_status'                => ['array', 'nullable'],
            'in_status.*'              => ['string', Rule::in(Invoice::STATUSES),],
            'date'                     => ['nullable', 'date',],
            'paid_date'                => ['nullable', 'date',],
            'due_date'                 => ['nullable', 'date',],
            'invoice_date'             => ['nullable', 'date',],
            'invoice_number'           => ['nullable', 'integer',],
            'search'                   => ['nullable', 'max:255',],
            'date_field'               => ['nullable', Rule::in(['created_at', 'updated_at', 'due_date', 'paid_at',]),],
            'from_date'                => ['nullable', 'date', 'before_or_equal:to_date',],
            'to_date'                  => ['nullable', 'date', 'after_or_equal:from_date',],
            'export'                   => ['nullable', 'boolean',],
            'with_detail'              => ['nullable', 'boolean',],
            'non_checked'              => ['nullable', 'boolean',],
            'is_credit'                => ['nullable', 'boolean',],
            'is_mass_payment'          => ['nullable', 'boolean',],
            'invoiceable_id'           => ['nullable', 'integer',],
            'invoiceable_type'         => ['nullable', 'max:255',],
            'items'                    => ['nullable', 'array',],
            'items.*.invoiceable_ids'  => ['nullable', 'array',],
            'items.*.invoiceable_type' => ['nullable', 'max:255',],
            'transaction_type'         => ['nullable']
        ];
    }
}
