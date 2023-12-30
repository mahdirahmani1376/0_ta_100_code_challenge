<?php

namespace App\Http\Requests\BankGateway;

use App\Models\BankGateway;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexBankGatewayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'sort' => ['string', 'nullable', Rule::in(get_sortable_items((new BankGateway())->getFillable()))],
            'sort_direction' => ['string', 'nullable', Rule::in('desc', 'asc')],
            'status' => ['string', 'nullable', Rule::in(BankGateway::STATUSES)],
            'export' => ['nullable', 'boolean',],
            'admin_id' => ['nullable', 'integer',],
        ];
    }
}

