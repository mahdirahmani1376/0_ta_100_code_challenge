<?php

namespace App\Repositories\BankAccount;

use App\Common\Repository\BaseRepository;
use App\Models\BankAccount;
use App\Repositories\BankAccount\Interface\BankAccountInterface;

class BankAccountRepository extends BaseRepository implements BankAccountInterface
{
    public string $model = BankAccount::class;
}
