<?php

namespace App\Repositories\ClientBankAccount\Interface;

use App\Models\ClientBankAccount;
use App\Repositories\Base\Interface\EloquentRepositoryInterface;

interface ClientBankAccountRepositoryInterface extends EloquentRepositoryInterface
{
    public function findSimilarWithZarinpalId(ClientBankAccount $clientBankAccount);
}
