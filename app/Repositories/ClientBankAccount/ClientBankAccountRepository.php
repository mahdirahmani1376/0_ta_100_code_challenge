<?php

namespace App\Repositories\ClientBankAccount;

use App\Common\Repository\BaseRepository;
use App\Models\ClientBankAccount;
use App\Repositories\ClientBankAccount\Interface\ClientBankAccountInterface;

class ClientBankAccountRepository extends BaseRepository implements ClientBankAccountInterface
{
    public string $model = ClientBankAccount::class;
}
