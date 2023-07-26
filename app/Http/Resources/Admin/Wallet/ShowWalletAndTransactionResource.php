<?php

namespace App\Http\Resources\Admin\Wallet;

use App\Models\Wallet;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowWalletAndTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var LengthAwarePaginator $d */
        $credit_transactions = $this['credit_transactions']; // TODO Maybe find a better and cleaner way to append credit_transactions into wallet and paginate it
        /** @var Wallet $this */
        return [
            'id' => $this->id,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'client_id' => $this->client_id,
            'name' => $this->name,
            'balance' => $this->balance,
            'is_active' => $this->is_active,
            'credit_transactions' => $credit_transactions->items(),
            'current_page' => $credit_transactions->currentPage(),
            'last_page' => $credit_transactions->lastPage(),
        ];
    }
}
