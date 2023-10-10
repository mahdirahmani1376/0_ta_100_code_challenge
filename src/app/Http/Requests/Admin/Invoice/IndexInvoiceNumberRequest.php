<?php

namespace App\Http\Requests\Admin\Invoice;

use App\Models\InvoiceNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexInvoiceNumberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sort' => ['string', 'nullable', Rule::in(get_sortable_items((new InvoiceNumber())->getFillable()))],
            'sortDirection' => ['string', 'nullable', Rule::in('desc', 'asc')],
            'invoice_id' => ['integer', 'nullable'],
            'search' => ['nullable', 'string'],
            'type' => ['nullable', Rule::in(InvoiceNumber::TYPES)],
            'status' => ['nullable', Rule::in(InvoiceNumber::STATUSES)],
        ];
    }
}
