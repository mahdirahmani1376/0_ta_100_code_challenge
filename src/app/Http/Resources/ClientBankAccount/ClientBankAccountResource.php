<?php

namespace App\Http\Resources\ClientBankAccount;

use App\Models\ClientBankAccount;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ClientBankAccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var ClientBankAccount $this */
        return [
            'id' => $this->id,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'profile_id' => $this->profile_id,
            'zarinpal_bank_account_id' => $this->zarinpal_bank_account_id,
            'bank_name' => $this->bank_name,
            'owner_name' => $this->owner_name,
            'sheba_number' => Str::start($this->sheba_number, 'IR'),
            'account_number' => $this->account_number,
            'card_number' => $this->card_number,
            'status' => $this->status,
        ];
    }
}
