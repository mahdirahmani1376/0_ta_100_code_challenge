<?php

namespace App\Actions\Admin\Wallet;

use App\Models\CreditTransaction;

class DeductBalanceAction
{
    private StoreCreditTransactionAction $storeCreditTransactionAction;

    public function __construct(StoreCreditTransactionAction $storeCreditTransactionAction)
    {
        $this->storeCreditTransactionAction = $storeCreditTransactionAction;
    }

    public function __invoke(int $clientId, array $data): CreditTransaction
    {
        if ($data['amount'] >= 1) {
            $data['amount'] = $data['amount'] * -1;
        }
        return ($this->storeCreditTransactionAction)($clientId, $data);
    }
}
