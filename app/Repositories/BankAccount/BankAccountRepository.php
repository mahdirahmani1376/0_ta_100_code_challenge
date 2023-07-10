<?php

namespace App\Repositories\BankAccount;

use App\Models\BankAccount;
use App\Repositories\BankAccount\Interface\BankAccountInterface;
use App\Repositories\Base\BaseRepository;

class BankAccountRepository extends BaseRepository implements BankAccountInterface
{
    public string $model = BankAccount::class;
}
