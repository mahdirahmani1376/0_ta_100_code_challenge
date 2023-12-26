<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListEverythingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'profile_id' => ['required', 'integer', 'exists:profiles,id',],
            'offset' => ['nullable', 'integer',],
            'per_page' => ['nullable', 'integer',],
        ];
    }
}
