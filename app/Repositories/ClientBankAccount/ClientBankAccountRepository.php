<?php

namespace App\Repositories\ClientBankAccount;

use App\Models\ClientBankAccount;
use App\Repositories\Base\BaseRepository;
use App\Repositories\ClientBankAccount\Interface\ClientBankAccountInterface;

class ClientBankAccountRepository extends BaseRepository implements ClientBankAccountInterface
{
    public string $model = ClientBankAccount::class;
}
