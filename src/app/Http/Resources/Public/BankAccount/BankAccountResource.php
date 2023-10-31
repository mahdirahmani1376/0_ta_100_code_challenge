<?php

namespace App\Http\Resources\Public\BankAccount;

use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankAccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var BankAccount $this */
        return [
            'id' => $this->id,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'display_order' => $this->display_order,
            'title' => $this->title,
            'status' => $this->status,
            'sheba_number' => $this->sheba_number,
            'account_number' => $this->account_number,
            'card_number' => $this->card_number,
        ];
    }
}
