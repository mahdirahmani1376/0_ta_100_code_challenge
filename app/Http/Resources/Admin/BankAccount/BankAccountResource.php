<?php

namespace App\Http\Resources\Admin\BankAccount;

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
            'deleted_at' => $this->deleted_at?->toDateTimeString(),
            'display_order' => $this->display_order,
            'title' => $this->title,
            'sheba_number' => $this->sheba_number,
            'account_number' => $this->account_number,
            'card_number' => $this->card_number,
            'rahkaran_id' => $this->rahkaran_id,
        ];
    }
}
