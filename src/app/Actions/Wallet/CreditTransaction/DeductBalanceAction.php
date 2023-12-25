<?php

namespace App\Actions\Wallet\CreditTransaction;

use App\Models\CreditTransaction;

class DeductBalanceAction
{
    public function __construct(private readonly StoreCreditTransactionAction $storeCreditTransactionAction)
    {
    }

    public function __invoke(int $profile_id, array $data): CreditTransaction
    {
        if ($data['amount'] >= 1) {
            $data['amount'] = $data['amount'] * -1;
        }

        return ($this->storeCreditTransactionAction)($profile_id, $data);
    }
}
