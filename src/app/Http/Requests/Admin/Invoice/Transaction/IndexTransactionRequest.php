<?php

namespace App\Http\Requests\Admin\Invoice\Transaction;

use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sort' => ['string', 'nullable', Rule::in(get_sortable_items((new Transaction())->getFillable()))],
            'sortDirection' => ['string', 'nullable', Rule::in('desc', 'asc')],
            'profile_id' => ['integer', 'nullable'],
            'invoice_id' => ['integer', 'nullable'],
            'search' => ['nullable', 'string', 'max:80'],
            'tracking_code' => ['integer', 'nullable'],
            'reference_id' => ['max:255', 'nullable'],
            'payment_method' => ['string', 'nullable',],
            'status' => ['string', 'nullable', Rule::in(Transaction::STATUSES)],
            'date' => ['date_format:Y-m-d', 'nullable'],
            'from_date' => ['nullable', 'date', 'date_format:Y-m-d', 'before_or_equal:to_date'],
            'to_date' => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:from_date'],
            "export" => ["nullable", "boolean"],
        ];
    }
}
