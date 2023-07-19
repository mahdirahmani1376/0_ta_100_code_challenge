<?php

namespace App\Http\Resources\Admin\Wallet;

use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowWalletResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Wallet $this */
        return [
            'id' => $this->id,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'deleted_at' => $this->deleted_at?->toDateTimeString(),
            'client_id' => $this->client_id,
            'name' => $this->name,
            'balance' => $this->balance,
            'is_active' => $this->is_active,
        ];
    }
}
