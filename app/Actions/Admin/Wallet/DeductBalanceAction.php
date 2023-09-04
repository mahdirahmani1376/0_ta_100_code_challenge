<?php

namespace App\Actions\Admin\Wallet;

use App\Models\CreditTransaction;

class DeductBalanceAction
{
    public function __construct(private readonly StoreCreditTransactionAction $storeCreditTransactionAction)
    {
    }

    public function __invoke(int $clientId, array $data): CreditTransaction
    {
        if ($data['amount'] >= 1) {
            $data['amount'] = $data['amount'] * -1;
        }
        return ($this->storeCreditTransactionAction)($clientId, $data);
    }
}
