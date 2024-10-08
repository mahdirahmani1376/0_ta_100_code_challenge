<?php

namespace App\Http\Resources\BankAccount;

use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class BankAccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var BankAccount $this */
        return [
            'id'             => $this->id,
            'created_at'     => $this->created_at?->toDateTimeString(),
            'updated_at'     => $this->updated_at?->toDateTimeString(),
            'order'          => $this->order,
            'title'          => $this->title,
            'status'         => $this->status,
            'sheba_number'   => Str::start($this->sheba_number, 'IR'),
            'account_number' => $this->account_number,
            'card_number'    => $this->card_number,
        ];
    }
}
