<?php

namespace App\Http\Resources\Wallet;

use App\Http\Resources\Wallet\CreditTransaction\CreditTransactionResource;
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
            'id'                  => $this->id,
            'created_at'          => $this->created_at?->toDateTimeString(),
            'updated_at'          => $this->updated_at?->toDateTimeString(),
            'profile_id'          => $this->profile_id,
            'name'                => $this->name,
            'balance'             => $this->balance,
            'is_active'           => $this->is_active,
            'credit_transactions' => CreditTransactionResource::collection($credit_transactions->items()),
            'current_page'        => $credit_transactions->currentPage(),
            'last_page'           => $credit_transactions->lastPage(),
            'per_page'            => $credit_transactions->perPage(),
            'total'               => $credit_transactions->total(),
        ];
    }
}
