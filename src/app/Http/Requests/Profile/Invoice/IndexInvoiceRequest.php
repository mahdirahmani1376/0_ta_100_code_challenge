<?php

namespace App\Http\Requests\Profile\Invoice;

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
            'profile_id' => ['required', 'numeric', 'exists:profiles,id',],
            'status' => ['nullable', 'string', Rule::in(Invoice::STATUSES)],
            'search' => "string|nullable",
            'sort' => ['string', 'nullable', Rule::in(get_sortable_items((new Invoice())->getFillable()))],
            'sortDirection' => ['string', 'nullable', Rule::in('desc', 'asc')],
        ];
    }
}
