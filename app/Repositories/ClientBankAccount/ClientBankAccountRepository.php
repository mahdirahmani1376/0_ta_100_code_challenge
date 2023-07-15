<?php

namespace App\Repositories\ClientBankAccount;

use App\Models\ClientBankAccount;
use App\Repositories\Base\BaseRepository;
use App\Repositories\ClientBankAccount\Interface\ClientBankAccountRepositoryInterface;

class ClientBankAccountRepository extends BaseRepository implements ClientBankAccountRepositoryInterface
{
    public string $model = ClientBankAccount::class;
}
