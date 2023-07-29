<?php

namespace App\Http\Requests\Admin\Invoice;

use App\Models\Invoice;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @throws BindingResolutionException
     */
    public function rules(): array
    {
        return [
            'sort' => ['string', 'nullable', Rule::in(get_sortable_items((new Invoice())->getFillable()))],
            'sortDirection' => ['string', 'nullable', Rule::in('desc', 'asc')],
            'client_id' => ['integer', 'nullable'],
            'invoice_id' => ['integer', 'nullable'],
            'payment_method' => ['string', 'nullable'],
            'status' => ['string', 'nullable', Rule::in(Invoice::STATUSES)],
            'date' => ['date_format:Y-m-d', 'nullable'],
            'paid_date' => ['date_format:Y-m-d', 'nullable'],
            'due_date' => ['date_format:Y-m-d', 'nullable'],
            'invoice_date' => ['date_format:Y-m-d', 'nullable'],
            'invoice_number' => ['integer', 'nullable'],
            'search' => ["nullable", "string"],
            'from_date' => ['nullable', 'date', 'date_format:Y-m-d', 'before_or_equal:to_date'],
            'to_date' => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:from_date'],
            "export" => ["nullable", "boolean"],
            'non_checked' => 'nullable|boolean'
        ];
    }
}
