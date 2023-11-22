<?php

namespace App\Http\Resources\Internal\Cloud\Wallet;

use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Wallet $this */
        return [
            'id' => $this->id,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'profile_id' => $this->profile_id,
            'name' => $this->name,
            'balance' => $this->balance,
            'is_active' => $this->is_active,
        ];
    }
}
