<?php

namespace App\Http\Resources\BankAccount;

use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowBankAccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var BankAccount $this */
        return [
            'id'     => $this->id,
            'title'  => $this->title,
            'status' => $this->status,
        ];
    }
}
